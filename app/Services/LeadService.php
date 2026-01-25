<?php

namespace App\Services;

use App\Repositories\LeadRepository;
use App\Models\Reason;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Lead;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class LeadService
{
    protected LeadRepository $repository;

    public function __construct(LeadRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Clear leads cache (safe for both taggable + non-taggable stores).
     */
    protected function flushCache(): void
    {
        if (cache()->getStore() instanceof \Illuminate\Cache\TaggableStore) {
            cache()->tags('leads')->flush();
            return;
        }
        // Avoid flushing entire cache to preserve round-robin pointer
        cache()->forget('leads.all');
        // Note: paginated keys vary; if needed, consider a more targeted cache strategy
    }

    /**
     * Cache repository for round-robin pointer and lock.
     * Prefer database store if configured so entries appear in cache tables.
     */
    private function rrCache(): \Illuminate\Contracts\Cache\Repository
    {
        if (config('cache.stores.database')) {
            return Cache::store('database');
        }
        return Cache::store(config('cache.default'));
    }

    /**
     * Get all leads (cached).
     */
    public function all(): Collection
    {
        if (cache()->getStore() instanceof \Illuminate\Cache\TaggableStore) {
            return cache()->tags('leads')->remember('leads.all', 300, function () {
                return $this->repository->all()->load(['governorate', 'source']);
            });
        }

        return cache()->remember('leads.all', 300, function () {
            return $this->repository->all()->load(['governorate', 'source']);
        });
    }

    /**
     * Get paginated leads (cached).
     */
    public function paginated(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = "leads.paginated.{$perPage}." . md5(json_encode($filters));

        if (cache()->getStore() instanceof \Illuminate\Cache\TaggableStore) {
            return cache()->tags('leads')->remember($cacheKey, 300, function () use ($perPage, $filters) {
                return $this->buildQuery($filters)->paginate($perPage);
            });
        }

        return cache()->remember($cacheKey, 300, function () use ($perPage, $filters) {
            return $this->buildQuery($filters)->paginate($perPage);
        });
    }

    /**
     * Build the query with filters.
     */
    protected function buildQuery(array $filters = [])
    {
        $query = $this->repository->query()->with(['governorate', 'source', 'employee']);

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value !== null && $value !== '') {
                    switch ($key) {
                        case 'assigned_to':
                            $query->where('assigned_to', $value);
                            break;
                        case 'status':
                            $query->where('status', $value);
                            break;
                        case 'date_range':
                            if (!empty($value['from'])) {
                                $query->whereDate('created_at', '>=', $value['from']);
                            }
                            if (!empty($value['to'])) {
                                $query->whereDate('created_at', '<=', $value['to']);
                            }
                            break;
                        default:
                            $query->where($key, $value);
                            break;
                    }
                }
            }
        }

        // Order by created_at descending (newest first)
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Find a lead by ID.
     */
    public function find(int $id): Lead
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new lead.
     */
    public function create(array $data): Lead
    {
        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'جديد';
        }

        // Prevent assignment if governorate or location is inactive (waiting)
        $shouldSkipAssignment = false;
        if (!empty($data['governorate_id'])) {
            $gov = \App\Models\Governorate::find($data['governorate_id']);
            if ($gov && $gov->is_active === false) {
                $shouldSkipAssignment = true;
            }
        }
        if (!empty($data['location_id'])) {
            $loc = \App\Models\Location::find($data['location_id']);
            if ($loc && $loc->is_active === false) {
                $shouldSkipAssignment = true;
            }
        }

        // If no assignment is specified, assign using strict round-robin among eligible sales employees
        /* if (empty($data['assigned_to']) && !$shouldSkipAssignment) {
            $nextEmployeeId = $this->getNextSalesEmployeeRoundRobin();
            if ($nextEmployeeId) {
                $data['assigned_to'] = $nextEmployeeId;
            }
        } */

        $lead = $this->repository->create($data);

        $this->flushCache();

        return $lead;
    }

    /**
     * Get the employee with the least number of leads.
     */
    public function getEmployeeWithLeastLeads(): ?int
    {
        // Get eligible active sales employees (department 7)
        $employees = \App\Models\User::where('type', 'employee')
            ->when(Schema::hasColumn('users', 'is_active'), function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('employee', function ($query) {
                $query->where('department_id', 7)
                      ->where('is_active', true)
                      ->whereDoesntHave('leaveRequests', function($l){
                          $l->approved()
                            ->whereDate('start_date', '<=', now()->toDateString())
                            ->whereDate('end_date', '>=', now()->toDateString());
                      });
            })
            ->pluck('id');


        if ($employees->isEmpty()) {
            return null;
        }

        // Get lead counts for each eligible employee using a more efficient query
        $employeeLeadCounts = \App\Models\Lead::selectRaw('assigned_to, COUNT(*) as lead_count')
            ->whereNotNull('assigned_to')
            ->whereIn('assigned_to', $employees)
            ->groupBy('assigned_to')
            ->pluck('lead_count', 'assigned_to')
            ->toArray();

        // Initialize counts for employees with no leads
        foreach ($employees as $employee) {
            if (!isset($employeeLeadCounts[$employee->id])) {
                $employeeLeadCounts[$employee->id] = 0;
            }
        }

        // Find employee with minimum lead count
        $minLeads = min($employeeLeadCounts);
        $employeesWithMinLeads = array_keys($employeeLeadCounts, $minLeads);

        // If multiple employees have the same minimum, choose randomly
        $selectedEmployeeId = $employeesWithMinLeads[array_rand($employeesWithMinLeads)];

        return $selectedEmployeeId;
    }

    /**
     * Get next sales employee (department 7) using round-robin assignment.
     * Falls back to least-leads strategy if no eligible employees found.
     */
    public function getNextSalesEmployeeRoundRobin(): ?int
    {
        // Eligible employees: active users of type employee with active employee in department_id = 7
        $eligible = \App\Models\User::where('type', 'employee')
            ->when(Schema::hasColumn('users', 'is_active'), function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('employee', function ($q) {
                $q->where('department_id', 7)
                  ->where('is_active', true)
                  ->whereDoesntHave('leaveRequests', function($l){
                      $l->approved()
                        ->whereDate('leave_requests.start_date', '<=', now()->toDateString())
                        ->whereDate('leave_requests.end_date', '>=', now()->toDateString());
                  });
            })

            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (empty($eligible)) {
            return $this->getEmployeeWithLeastLeads();
        }

        $keyIndex = 'leads:rr:sales:index';
        $maxIndex = count($eligible) - 1;

        // Try to use atomic lock when available; otherwise, do best-effort
        try {
            $cacheRepo = $this->rrCache();
            if (method_exists($cacheRepo, 'lock')) {
                $lock = $cacheRepo->lock('leads:rr:sales:lock', 5);
                return $lock->block(3, function () use ($keyIndex, $eligible, $maxIndex) {
                    $repo = $this->rrCache();
                    $currentIndex = (int) $repo->get($keyIndex, 0);
                    if ($currentIndex < 0 || $currentIndex > $maxIndex) {
                        $currentIndex = 0;
                    }
                    $employeeId = $eligible[$currentIndex];
                    $nextIndex = ($currentIndex + 1) % ($maxIndex + 1);
                    $repo->forever($keyIndex, $nextIndex);
                    return $employeeId;
                });
            }
        } catch (\Throwable $e) {
            // Ignore lock errors and fall through to best-effort below
        }

        // Best-effort without lock
        $cacheRepo = $this->rrCache();
        $currentIndex = (int) $cacheRepo->get($keyIndex, 0);
        if ($currentIndex < 0 || $currentIndex > $maxIndex) {
            $currentIndex = 0;
        }
        $employeeId = $eligible[$currentIndex];
        $nextIndex = ($currentIndex + 1) % ($maxIndex + 1);
        $cacheRepo->forever($keyIndex, $nextIndex);
        return $employeeId;
    }

    /**
     * Distribute a list of lead IDs across eligible employees in round-robin order.
     * Returns the selected employee ID for each lead ID.
     */
    public function distributeLeadsRoundRobin(array $leadIds): array
    {
        if (empty($leadIds)) {
            return [];
        }


        $eligible = \App\Models\User::where('type', 'employee')
            ->when(Schema::hasColumn('users', 'is_active'), function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('employee', function ($q) {
                $q->where('department_id', 7)
                  ->where('is_active', true)
                  ->whereDoesntHave('leaveRequests', function($l){
                      $l->approved()
                        ->whereDate('leave_requests.start_date', '<=', now()->toDateString())
                        ->whereDate('leave_requests.end_date', '>=', now()->toDateString());
                  });
            })
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (empty($eligible)) {
            return [];
        }

        $assignments = [];
        $cacheRepo = $this->rrCache();
        $currentIndex = (int) $cacheRepo->get('leads:rr:sales:index', 0);
        $maxIndex = count($eligible) - 1;
        if ($currentIndex < 0 || $currentIndex > $maxIndex) {
            $currentIndex = 0;
        }

        foreach ($leadIds as $leadId) {
            $assignments[$leadId] = $eligible[$currentIndex];
            $currentIndex = ($currentIndex + 1) % ($maxIndex + 1);
        }

        $cacheRepo->forever('leads:rr:sales:index', $currentIndex);
        return $assignments;
    }

     public function distributeInterviewsRoundRobin(array $leadIds): array
    {
        if (empty($leadIds)) {
            return [];
        }


        $eligible = \App\Models\User::where('type', 'employee')
            ->when(Schema::hasColumn('users', 'is_active'), function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('employee', function ($q) {
                $q->where('department_id', 7)
                  ->where('is_active', true)
                  ->whereDoesntHave('leaveRequests', function($l){
                      $l->approved()
                        ->whereDate('leave_requests.start_date', '<=', now()->toDateString())
                        ->whereDate('leave_requests.end_date', '>=', now()->toDateString());
                  });
            })
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (empty($eligible)) {
            return [];
        }

        $assignments = [];
        $cacheRepo = $this->rrCache();
        $currentIndex = (int) $cacheRepo->get('interviews:rr:sales:index', 0);
        $maxIndex = count($eligible) - 1;
        if ($currentIndex < 0 || $currentIndex > $maxIndex) {
            $currentIndex = 0;
        }

        foreach ($leadIds as $leadId) {
            $assignments[$leadId] = $eligible[$currentIndex];
            $currentIndex = ($currentIndex + 1) % ($maxIndex + 1);
        }

        $cacheRepo->forever('interviews:rr:sales:index', $currentIndex);
        return $assignments;
    }

    /**
     * Get employee lead counts for display purposes.
     */
    public function getEmployeeLeadCounts(): array
    {
        $employees = \App\Models\User::where('type', 'employee')->get();
        $employeeLeadCounts = \App\Models\Lead::selectRaw('assigned_to, COUNT(*) as lead_count')
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->pluck('lead_count', 'assigned_to')
            ->toArray();

        $result = [];
        foreach ($employees as $employee) {
            $result[$employee->id] = [
                'name' => $employee->employee?->name ?? $employee->name,
                'count' => $employeeLeadCounts[$employee->id] ?? 0
            ];
        }

        return $result;
    }

    /**
     * Add a follow-up to a lead.
     */
    public function addFollowup(int $id, array $data): Lead
    {
        $lead = $this->find($id);
        $followUps = $lead->follow_ups ?? [];

        // Validate reasons
        if (!empty($data['reasons'])) {
            foreach ($data['reasons'] as $reasonId) {
                Reason::findOrFail($reasonId);
            }
        }

        $followUps[] = [
            'call_number' => count($followUps) + 1,
            'notes'       => $data['notes'] ?? null,
            'reasons'     => $data['reasons'] ?? [],
            'created_at'  => now()->toDateTimeString(),
        ];

        $lead->update(['follow_ups' => json_encode($followUps)]);

        $this->flushCache();

        return $lead;
    }

    /**
     * Update lead status.
     */
    public function updateStatus(int $id, string $status): Lead
    {
        $lead = $this->find($id);
        $lead->update(['status' => $status]);

        $this->flushCache();

        return $lead;
    }

    /**
     * Update an existing lead.
     */
    public function update(int $id, array $data): Lead
    {
        $lead = $this->find($id);

        // If no assignment is specified and the lead is currently unassigned, use round-robin
        $shouldSkipAssignment = false;
        $govId = $data['governorate_id'] ?? $lead->governorate_id;
        $locId = $data['location_id'] ?? $lead->location_id;
        if (!empty($govId)) {
            $gov = \App\Models\Governorate::find($govId);
            if ($gov && $gov->is_active === false) {
                $shouldSkipAssignment = true;
            }
        }
        if (!empty($locId)) {
            $loc = \App\Models\Location::find($locId);
            if ($loc && $loc->is_active === false) {
                $shouldSkipAssignment = true;
            }
        }

        if (empty($data['assigned_to']) && empty($lead->assigned_to) && !$shouldSkipAssignment) {
            $data['assigned_to'] = $this->getNextSalesEmployeeRoundRobin();
        }

        // If status is empty, keep the existing status
        if (empty($data['status'])) {
            unset($data['status']);
        }

        $lead = $this->repository->update($lead, $data);

        $this->flushCache();

        return $lead;
    }

    /**
     * Delete a lead.
     */
    public function delete(int $id): ?bool
    {
        $lead = $this->find($id);
        $result = $this->repository->delete($lead);

        $this->flushCache();

        return $result;
    }
}
