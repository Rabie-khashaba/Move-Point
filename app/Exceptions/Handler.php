<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // default reporting
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Validation errors → keep Laravel's structure but wrap message
        if ($e instanceof ValidationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات المدخلة غير صحيحة. الرجاء تصحيح الأخطاء والمحاولة مرة أخرى.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'البيانات المدخلة غير صحيحة. الرجاء تصحيح الأخطاء والمحاولة مرة أخرى.')
                ->withErrors($e->errors())
                ->withInput($request->except($this->dontFlash));
        }

        // Unauthenticated
        if ($e instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح: الرجاء تسجيل الدخول.',
                ], 401);
            }
            return redirect()->guest(route('login'))->with('error', 'الرجاء تسجيل الدخول للمتابعة.');
        }

        // Unauthorized (policies/gates)
        if ($e instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتنفيذ هذه العملية.',
                ], 403);
            }
            return redirect()->back()->with('error', 'غير مصرح لك بتنفيذ هذه العملية.');
        }

        // Model not found → 404
        if ($e instanceof ModelNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'العنصر المطلوب غير موجود.',
                ], 404);
            }
            return redirect()->back()->with('error', 'العنصر المطلوب غير موجود.');
        }

        // Database query errors
        if ($e instanceof QueryException) {
            $status = 500;
            $message = 'تعذر تنفيذ العملية على قاعدة البيانات. الرجاء المحاولة لاحقاً.';

            // Specific message for unique phone violations on leads
            $sqlMessage = $e->getMessage();
            if (stripos($sqlMessage, 'leads') !== false && stripos($sqlMessage, 'phone') !== false) {
                if (stripos($sqlMessage, 'duplicate') !== false || stripos($sqlMessage, 'unique') !== false) {
                    $message = 'هذا الرقم مسجل مسبقاً لدينا.';
                    $status = 422;
                }
            }

            if ($request->expectsJson()) {
                $payload = [
                    'success' => false,
                    'message' => $message,
                ];
                if (config('app.debug')) {
                    $payload['error'] = 'QueryException';
                    $payload['sql_state'] = $e->getCode();
                    $payload['details'] = $e->getMessage();
                }
                return response()->json($payload, $status);
            }
            return redirect()->back()->with('error', $message);
        }

        // HTTP exceptions keep their status code, otherwise 500
        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        $message = $status === 404
            ? 'العنصر المطلوب غير موجود.'
            : ($status === 403
                ? 'غير مصرح لك بتنفيذ هذه العملية.'
                : 'حدث خطأ غير متوقع. الرجاء المحاولة لاحقاً.');

        if ($request->expectsJson()) {
            $payload = [
                'success' => false,
                'message' => $message,
            ];
            if (config('app.debug')) {
                $payload['error'] = class_basename($e);
                $payload['details'] = $e->getMessage();
            }
            return response()->json($payload, $status);
        }

        // Web responses → flash a unified message
        return redirect()->back()->with('error', $message);
    }
}
