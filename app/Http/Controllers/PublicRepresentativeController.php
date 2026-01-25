<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Governorate;
use App\Models\Location;
use App\Services\RepresentativeNoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicRepresentativeController extends Controller
{
    protected $representativeService;

    public function __construct(RepresentativeNoService $representativeService)
    {
        $this->representativeService = $representativeService;
    }

    /**
     * Show the public representative registration form
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $governorates = Governorate::all();
        $locations = Location::all();

        return view('public.representative-registration', compact('companies', 'governorates', 'locations'));
    }

    /**
     * Store a new representative from public form
     */
    public function store(Request $request)
    {
        Log::info('Public representative registration started', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:11|unique:users,phone',
                'address' => 'required|string|max:500',
                'contact' => 'required|digits:11|unique:users,phone',
                'national_id' => 'required|digits:14|unique:representatives,national_id',
                'company_id' => 'required|exists:companies,id',
                'bank_account' => 'nullable|string|max:255',
                'code' => 'nullable|string|max:255',
                'attachments.0' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // البطاقة (وجه أول)
                'attachments.1' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // البطاقة (خلف)
                'attachments.2' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // فيش
                'attachments.3' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // شهادة ميلاد
                'attachments.4' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // ايصال الامانه
                'attachments.5' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // رخصة القيادة
                'attachments.6' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // رخصة السيارة وجه أول
                'attachments.7' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // رخصة السيارة وجه ثاني
                'governorate_id' => 'required|exists:governorates,id',
                'location_id' => 'nullable|exists:locations,id',
                'home_location' => 'nullable|url|max:500',
            ], [
                // Custom validation messages in Arabic
                'name.required' => 'اسم المندوب مطلوب',
                'name.string' => 'اسم المندوب يجب أن يكون نص',
                'name.max' => 'اسم المندوب لا يجب أن يتجاوز 255 حرف',

                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.digits' => 'رقم الهاتف يجب أن يكون 11 رقم',
                'phone.unique' => 'هذا الرقم مستخدم من قبل، يرجى استخدام رقم آخر',

                'address.required' => 'العنوان مطلوب',
                'address.string' => 'العنوان يجب أن يكون نص',
                'address.max' => 'العنوان لا يجب أن يتجاوز 500 حرف',

                'contact.required' => 'رقم التواصل مطلوب',
                'contact.digits' => 'رقم التواصل يجب أن يكون 11 رقم',
                'contact.unique' => 'هذا الرقم مستخدم من قبل، يرجى استخدام رقم آخر',

                'national_id.required' => 'الرقم القومي مطلوب',
                'national_id.digits' => 'الرقم القومي يجب أن يكون 14 رقم',
                'national_id.unique' => 'هذا الرقم القومي مستخدم من قبل',

                'company_id.required' => 'الشركة مطلوبة',
                'company_id.exists' => 'الشركة المحددة غير موجودة',

                'bank_account.string' => 'رقم المحفظة يجب أن يكون نص',
                'bank_account.max' => 'رقم المحفظة لا يجب أن يتجاوز 255 حرف',

                'code.string' => 'كود المندوب يجب أن يكون نص',
                'code.max' => 'كود المندوب لا يجب أن يتجاوز 255 حرف',

                'attachments.0.required' => 'صورة البطاقة (الوجه الأول) مطلوبة',
                'attachments.0.file' => 'صورة البطاقة (الوجه الأول) يجب أن يكون ملف',
                'attachments.0.mimes' => 'صورة البطاقة (الوجه الأول) يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.0.max' => 'صورة البطاقة (الوجه الأول) لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.1.required' => 'صورة البطاقة (الخلف) مطلوبة',
                'attachments.1.file' => 'صورة البطاقة (الخلف) يجب أن يكون ملف',
                'attachments.1.mimes' => 'صورة البطاقة (الخلف) يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.1.max' => 'صورة البطاقة (الخلف) لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.2.file' => 'صورة الفيش يجب أن يكون ملف',
                'attachments.2.mimes' => 'صورة الفيش يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.2.max' => 'صورة الفيش لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.3.file' => 'شهادة الميلاد يجب أن يكون ملف',
                'attachments.3.mimes' => 'شهادة الميلاد يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.3.max' => 'شهادة الميلاد لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.4.file' => 'رخصة القيادة يجب أن يكون ملف',
                'attachments.4.mimes' => 'رخصة القيادة يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.4.max' => 'رخصة القيادة لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.5.file' => 'رخصة السيارة وجه اول يجب أن يكون ملف',
                'attachments.5.mimes' => 'رخصة السيارة وجه اول يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.5.max' => 'رخصة السيارة وجه اول لا يجب أن يتجاوز 2 ميجابايت',

                'attachments.6.file' => 'رخصة السيارة وجه ثاني يجب أن يكون ملف',
                'attachments.6.mimes' => 'رخصة السيارة وجه ثاني يجب أن يكون من نوع: PDF, JPG, JPEG, PNG',
                'attachments.6.max' => 'رخصة السيارة وجه ثاني لا يجب أن يتجاوز 2 ميجابايت',

                'governorate_id.required' => 'المحافظة مطلوبة',
                'governorate_id.exists' => 'المحافظة المحددة غير موجودة',

                'location_id.exists' => 'المنطقة المحددة غير موجودة',

                'home_location.url' => 'رابط الموقع يجب أن يكون رابط صحيح',
                'home_location.max' => 'رابط الموقع لا يجب أن يتجاوز 500 حرف',
            ]);

            Log::info('Public representative registration validation passed', [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'company_id' => $validated['company_id'],
                'governorate_id' => $validated['governorate_id'],
                'attachments_count' => count(array_filter($validated['attachments'] ?? []))
            ]);

            // Set default values for public registration
            $validated['is_active'] = true; // New representatives need approval
            $validated['created_by'] = null; // Public registration

            // Set default values for removed fields
            $validated['salary'] = 0; // Default salary
            $validated['start_date'] = now()->subDays(10)->format('Y-m-d'); // Start date 10 days ago

            Log::info('Calling RepresentativeNoService to create representative', [
                'phone' => $validated['phone'],
                'name' => $validated['name']
            ]);

            $representative = $this->representativeService->create($validated);

            Log::info('Public representative registration completed successfully', [
                'representative_id' => $representative->id,
                'user_id' => $representative->user_id,
                'phone' => $validated['phone'],
                'name' => $validated['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلب التسجيل بنجاح! سنتواصل معك قريباً للمراجعة والموافقة.',
                'redirect_url' => route('public.representative.success')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorCount = count($errors);

            Log::warning('Validation error in PublicRepresentativeController@store', [
                'errors' => $errors,
                'error_count' => $errorCount,
                'input' => $request->except(['attachments']),
                'phone' => $request->input('phone'),
                'name' => $request->input('name')
            ]);

            // Generate specific error message based on validation errors
            $errorMessage = 'يرجى تصحيح الأخطاء أدناه';
            if ($errorCount === 1) {
                $firstError = array_values($errors)[0][0];
                $errorMessage = $firstError;
            } elseif ($errorCount > 1) {
                $errorMessage = "يوجد {$errorCount} أخطاء يجب تصحيحها";
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => $errors,
                'error_count' => $errorCount
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->getCode();
            $errorMessage = 'حدث خطأ في قاعدة البيانات. يرجى المحاولة مرة أخرى أو التواصل معنا.';

            // Handle specific database errors
            if ($errorCode === 23000) { // Duplicate entry
                if (strpos($e->getMessage(), 'phone') !== false) {
                    $errorMessage = 'هذا الرقم مستخدم من قبل، يرجى استخدام رقم آخر';
                } elseif (strpos($e->getMessage(), 'national_id') !== false) {
                    $errorMessage = 'هذا الرقم القومي مستخدم من قبل، يرجى التحقق من البيانات';
                } elseif (strpos($e->getMessage(), 'contact') !== false) {
                    $errorMessage = 'رقم التواصل مستخدم من قبل، يرجى استخدام رقم آخر';
                } else {
                    $errorMessage = 'البيانات المدخلة مستخدمة من قبل، يرجى التحقق من البيانات';
                }
            } elseif ($errorCode === 22001) { // Data too long
                $errorMessage = 'أحد الحقول المدخلة أطول من المسموح به';
            } elseif ($errorCode === 1048) { // Column cannot be null
                $errorMessage = 'يرجى ملء جميع الحقول المطلوبة';
            }

            Log::error('Database error in PublicRepresentativeController@store: ' . $e->getMessage(), [
                'error_code' => $errorCode,
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'input' => $request->except(['attachments']),
                'phone' => $request->input('phone'),
                'name' => $request->input('name')
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => 'database',
                'error_code' => $errorCode
            ], 500);
        } catch (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            Log::warning('Rate limit exceeded in PublicRepresentativeController@store', [
                'ip' => $request->ip(),
                'phone' => $request->input('phone')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تم إرسال طلبات كثيرة. يرجى الانتظار قليلاً قبل المحاولة مرة أخرى.'
            ], 429);
        } catch (\Exception $e) {
            $errorType = get_class($e);
            $errorMessage = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى أو التواصل معنا عبر الهاتف.';

            // Handle specific exception types
            if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
                $errorMessage = 'حجم الملفات المرسلة كبير جداً. يرجى تقليل حجم الملفات أو إرسال ملفات أقل';
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
                $errorMessage = 'تم إرسال طلبات كثيرة جداً. يرجى الانتظار قليلاً قبل المحاولة مرة أخرى';
            } elseif ($e instanceof \Illuminate\Filesystem\FileNotFoundException) {
                $errorMessage = 'خطأ في رفع الملفات. يرجى التأكد من صحة الملفات وإعادة المحاولة';
            } elseif ($e instanceof \Illuminate\Contracts\Filesystem\FileNotFoundException) {
                $errorMessage = 'خطأ في حفظ الملفات. يرجى إعادة المحاولة';
            }

            Log::error('Unexpected error in PublicRepresentativeController@store: ' . $e->getMessage(), [
                'error_type' => $errorType,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['attachments']),
                'phone' => $request->input('phone'),
                'name' => $request->input('name')
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => $errorType
            ], 500);
        }
    }
}
