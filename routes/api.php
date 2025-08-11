<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ActionsController;
use App\Http\Controllers\AIPromptTemplateController;
use App\Http\Controllers\AllowFileExtensionController;
use App\Http\Controllers\ArchiveDocumentController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentCommentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserClaimController;
use App\Http\Controllers\EmailSMTPSettingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentPermissionController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\DocumentAuditTrailController;
use App\Http\Controllers\DocumentSharebaleLinkController;
use App\Http\Controllers\DocumentTokenController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FileRequestController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RoleUsersController;
use App\Http\Controllers\LoginAuditController;
use App\Http\Controllers\PageHelperController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\WorkflowStepController;
use App\Http\Controllers\WorkflowTransitionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentStatusController;
use App\Http\Controllers\DocumentWorkflowController;
use App\Http\Controllers\FileRequestDocumentController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\WorkflowLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/logout', 'logout');
});

Route::post('/user/forgotPassword', [UserController::class, 'forgotpassword']);
Route::get('/user/getResetinfo/{id}', [UserController::class, 'getUserInfoForResetPassword']);
Route::post('user/recoverPassword', [UserController::class, 'resetPassword']);
Route::get('document/{id}/officeviewer', [DocumentController::class, 'officeviewer']);
Route::get('/companyprofile', [CompanyProfileController::class, 'getCompanyProfile']);
Route::post('/companyprofile/activate_license', [CompanyProfileController::class, 'updateLicense']);


Route::middleware(['auth'])->group(function () {
    //common dropdowns
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/document-status', [DocumentStatusController::class, 'index']);
    Route::get('/aIPromptTemplate', [AIPromptTemplateController::class, 'index']);
    Route::get('storage/key', [CompanyProfileController::class, 'getStorageOpenAiKey']);
    Route::get('/storage', [CompanyProfileController::class, 'getStorage']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-dropdown', [UserController::class, 'dropdown']);
    Route::post('/user/changepassword', [UserController::class, 'changePassword']);
    Route::put('/users/profile', [UserController::class, 'updateUserProfile']);
    Route::get('/role-dropdown', [RoleController::class, 'dropdown']);
    Route::post('/documentAuditTrail', [DocumentAuditTrailController::class, 'saveDocumentAuditTrail']);
    //workflow
    Route::get('/workflow/{id}/visualWorkflow', [WorkflowController::class, 'visualWorkflow']);
    Route::get('/documentWorkflow/{id}/visualWorkflow', [DocumentWorkflowController::class, 'visualWorkflow']);
    Route::post('/documentWorkflow/performNextTransition', [DocumentWorkflowController::class, 'performNextTransition']);

    Route::middleware('hasToken:ARCHIVE_DOCUMENT_VIEW_DOCUMENTS')->group(function () {
        Route::get('/archived-documents', [ArchiveDocumentController::class, 'getDocuments']);
    });

    Route::middleware('hasToken:ARCHIVE_DOCUMENT_RESTORE_DOCUMENT')->group(function () {
        Route::put('/archived-documents/{id}/restore', [ArchiveDocumentController::class, 'restoreDocument']);
    });

    Route::middleware('hasToken:SETTING_MANAGE_PROFILE')->group(function () {
        Route::post('/companyProfile', [CompanyProfileController::class, 'updateCompanyProfile']);
    });

    Route::middleware('hasToken:SETTINGS_STORAGE_SETTINGS')->group(function () {
        Route::post('/storage', [CompanyProfileController::class, 'updateStorage']);
    });

    Route::middleware('hasToken:SETTINGS_MANAGE_OPEN_AI_API_KEY')->group(function () {
        Route::post('storage', [CompanyProfileController::class, 'saveOpenAiKey']);
    });

    Route::group(['middleware' => ['hasToken:USER_VIEW_USERS']], function () {
        Route::get('/user', [UserController::class, 'index']);
    });

    Route::middleware('hasToken:USER_CREATE_USER')->group(function () {
        Route::post('/user', [UserController::class, 'create']);
    });

    Route::middleware('hasToken:USER_EDIT_USER')->group(function () {
        Route::put('/user/{id}', [UserController::class, 'update']);
    });

    Route::middleware('hasToken:USER_DELETE_USER')->group(function () {
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('hasToken:USER_EDIT_USER')->group(function () {
        Route::get('/user/{id}', [UserController::class, 'edit']);
    });

    Route::middleware('hasToken:USER_RESET_PASSWORD')->group(function () {
        Route::post('/user/resetpassword', [UserController::class, 'submitResetPassword']);
    });

    Route::middleware('hasToken:USER_ASSIGN_PERMISSION')->group(function () {
        Route::put('/userClaim/{id}', [UserClaimController::class, 'update']);
    });

    Route::middleware('hasToken:DASHBOARD_VIEW_DASHBOARD')->group(function () {
        Route::get('/dashboard/reminders/{month}/{year}', [DashboardController::class, 'getReminders']);
        Route::get('/Dashboard/GetDocumentByCategory', [DocumentController::class, 'getDocumentsByCategoryQuery']);
    });

    Route::get('/category/dropdown', [CategoryController::class, 'GetAllCategoriesForDropDown']);
    Route::middleware('hasToken:DOCUMENT_CATEGORY_MANAGE_DOCUMENT_CATEGORY')->group(function () {
        Route::get('category', [CategoryController::class, 'index']);
        Route::post('/category', [CategoryController::class, 'create']);
        Route::put('/category/{id}', [CategoryController::class, 'update']);
        Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
        Route::get('/category/{id}/subcategories', [CategoryController::class, 'subcategories']);
    });

    Route::get('/pages', [PagesController::class, 'index']);
    Route::post('/pages', [PagesController::class, 'create']);
    Route::put('/pages/{id}', [PagesController::class, 'update']);
    Route::delete('/pages/{id}', [PagesController::class, 'destroy']);

    Route::get('/actions', [ActionsController::class, 'index']);
    Route::post('/actions', [ActionsController::class, 'create']);
    Route::put('/actions/{id}', [ActionsController::class, 'update']);
    Route::delete('/actions/{id}', [ActionsController::class, 'destroy']);

    Route::group(['middleware' => ['hasToken:ROLE_VIEW_ROLES']], function () {
        Route::get('/role', [RoleController::class, 'index']);
    });

    Route::middleware('hasToken:ROLE_CREATE_ROLE')->group(function () {
        Route::post('/role', [RoleController::class, 'create']);
    });

    Route::middleware('hasToken:ROLE_EDIT_ROLE')->group(function () {
        Route::put('/role/{id}', [RoleController::class, 'update']);
    });

    Route::middleware('hasToken:ROLE_DELETE_ROLE')->group(function () {
        Route::delete('/role/{id}', [RoleController::class, 'destroy']);
    });

    Route::middleware('hasToken:ROLE_EDIT_ROLE')->group(function () {
        Route::get('/role/{id}', [RoleController::class, 'edit']);
    });

    Route::middleware('hasToken:EMAIL_MANAGE_SMTP_SETTINGS')->group(function () {
        Route::get('/emailSMTPSetting', [EmailSMTPSettingController::class, 'index']);
        Route::post('/emailSMTPSetting', [EmailSMTPSettingController::class, 'create']);
        Route::put('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'update']);
        Route::delete('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'destroy']);
        Route::get('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'edit']);
    });

    Route::get('/document/{id}/download/{isVersion}', [DocumentController::class, 'downloadDocument']);
    Route::get('/document/{id}/readText/{isVersion}', [DocumentController::class, 'readTextDocument']);
    Route::middleware('hasToken:ALL_DOCUMENTS_VIEW_DOCUMENTS')->group(function () {
        Route::get('/documents', [DocumentController::class, 'getDocuments']);
    });

    Route::middleware('hasToken:DEEP_SEARCH_DEEP_SEARCH')->group(function () {
        Route::get('/documents/deep-search', [DocumentController::class, 'getDeepSearchDocuments']);
    });

    Route::middleware('hasToken:DEEP_SEARCH_ADD_INDEXING')->group(function () {
        Route::post('/documents/deep-search/{id}', [DocumentController::class, 'addDOocumentToDeepSearch']);
    });

    Route::middleware('hasToken:DEEP_SEARCH_REMOVE_INDEXING')->group(function () {
        Route::delete('/documents/deep-search/{id}', [DocumentController::class, 'removeDocumentFromDeepSearch']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_CREATE_DOCUMENT, ASSIGNED_DOCUMENTS_CREATE_DOCUMENT, BULK_DOCUMENT_UPLOAD']], function () {
        Route::post('/document', [DocumentController::class, 'saveDocument']);
    });

    Route::get('/document/assignedDocuments', [DocumentController::class, 'assignedDocuments']);

    Route::get('/document/{id}', [DocumentController::class, 'getDocumentbyId']);

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_EDIT_DOCUMENT,
        ASSIGNED_DOCUMENTS_EDIT_DOCUMENT,
        ALL_DOCUMENTS_VIEW_DETAIL,
        ASSIGNED_DOCUMENTS_VIEW_DETAIL
    ']], function () {
        Route::get('/document/{id}/getMetatag', [DocumentController::class, 'getDocumentMetatags']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_ARCHIVE_DOCUMENT,ASSIGNED_DOCUMENTS_ARCHIVE_DOCUMENT']], function () {
        Route::delete('/document/{id}/archive', [DocumentController::class, 'archiveDocument']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_EDIT_DOCUMENT,ASSIGNED_DOCUMENTS_EDIT_DOCUMENT']], function () {
        Route::put('/document/{id}', [DocumentController::class, 'updateDocument']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_DELETE_DOCUMENT,ASSIGNED_DOCUMENTS_DELETE_DOCUMENT,ARCHIVE_DOCUMENT_DELETE_DOCUMENTS']], function () {
        Route::delete('/document/{id}', [DocumentController::class, 'deleteDocument']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_VIEW_DETAIL,
    ASSIGNED_DOCUMENTS_VIEW_DETAIL,
    DOCUMENT_AUDIT_TRAIL_VIEW_DOCUMENT_AUDIT_TRAIL']], function () {
        Route::get('/documentAuditTrail', [DocumentAuditTrailController::class, 'getDocumentAuditTrails']);
    });

    Route::group(['middleware' => [
        'hasToken:ALL_DOCUMENTS_MANAGE_COMMENT,
        ASSIGNED_DOCUMENTS_MANAGE_COMMENT,
        ALL_DOCUMENTS_VIEW_DETAIL,
        ASSIGNED_DOCUMENTS_VIEW_DETAIL'
    ]], function () {
        Route::get('/documentComment/{documentId}', [DocumentCommentController::class, 'index']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_MANAGE_COMMENT,ASSIGNED_DOCUMENTS_MANAGE_COMMENT']], function () {
        Route::delete('/documentComment/{id}', [DocumentCommentController::class, 'destroy']);
        Route::post('/documentComment', [DocumentCommentController::class, 'saveDocumentComment']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,
        ASSIGNED_DOCUMENTS_SHARE_DOCUMENT,
        ALL_DOCUMENTS_VIEW_DETAIL,
        ASSIGNED_DOCUMENTS_VIEW_DETAIL']], function () {
        Route::get('/DocumentRolePermission/{id}', [DocumentPermissionController::class, 'getDocumentPermissions']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,ASSIGNED_DOCUMENTS_SHARE_DOCUMENT']], function () {
        Route::post('/documentRolePermission', [DocumentPermissionController::class, 'addDocumentRolePermission']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,ASSIGNED_DOCUMENTS_SHARE_DOCUMENT']], function () {
        Route::post('/documentUserPermission', [DocumentPermissionController::class, 'addDocumentUserPermission']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,ASSIGNED_DOCUMENTS_SHARE_DOCUMENT']], function () {
        Route::post('/documentRolePermission/multiple', [DocumentPermissionController::class, 'multipleDocumentsToUsersAndRoles']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,ASSIGNED_DOCUMENTS_SHARE_DOCUMENT']], function () {
        Route::delete('/documentUserPermission/{id}', [DocumentPermissionController::class, 'deleteDocumentUserPermission']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_SHARE_DOCUMENT,ASSIGNED_DOCUMENTS_SHARE_DOCUMENT']], function () {
        Route::delete('/documentRolePermission/{id}', [DocumentPermissionController::class, 'deleteDocumentRolePermission']);
    });

    Route::get('/document/{id}/isDownloadFlag', [DocumentPermissionController::class, 'getIsDownloadFlag']);

    Route::get('/documentversion/{documentId}', [DocumentVersionController::class, 'index']);

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_VIEW_DOCUMENTS,ALL_DOCUMENTS_UPLOAD_NEW_VERSION,ASSIGNED_DOCUMENTS_UPLOAD_NEW_VERSION']], function () {
        Route::post('/documentversion', [DocumentVersionController::class, 'saveNewVersionDocument']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_RESTORE_VERSION,ALL_DOCUMENTS_RESTORE_VERSION']], function () {
        Route::post('/documentversion/{id}/restore/{versionId}', [DocumentVersionController::class, 'restoreDocumentVersion']);
    });

    Route::get('/documentToken/{documentId}/token', [DocumentTokenController::class, 'getDocumentToken']);
    Route::delete('/documentToken/{token}', [DocumentTokenController::class, 'deleteDocumentToken']);
    Route::post('/reminder/document', [ReminderController::class, 'addReminder']);
    Route::get('/reminder/{id}/myreminder', [ReminderController::class, 'edit']);

    Route::middleware('hasToken:USER_ASSIGN_USER_ROLE')->group(function () {
        Route::get('/roleusers/{roleId}', [RoleUsersController::class, 'getRoleUsers']);
    });

    Route::middleware('hasToken:USER_ASSIGN_USER_ROLE')->group(function () {
        Route::put('/roleusers/{roleId}', [RoleUsersController::class, 'updateRoleUsers']);
    });

    Route::middleware('hasToken:LOGIN_AUDIT_VIEW_LOGIN_AUDIT_LOGS')->group(function () {
        Route::get('/loginAudit', [LoginAuditController::class, 'getLoginAudit']);
    });

    Route::group(['middleware' => ['hasToken:REMINDER_VIEW_REMINDERS,
        ALL_DOCUMENTS_VIEW_DETAIL,
        ASSIGNED_DOCUMENTS_VIEW_DETAIL']], function () {
        Route::get('/reminder/all', [ReminderController::class, 'getReminders']);
    });

    Route::middleware('hasToken:REMINDER_CREATE_REMINDER')->group(function () {
        Route::post('/reminder', [ReminderController::class, 'addReminder']);
    });

    Route::middleware('hasToken:REMINDER_EDIT_REMINDER')->group(function () {
        Route::get('/reminder/{id}', [ReminderController::class, 'edit']);
    });

    Route::middleware('hasToken:REMINDER_EDIT_REMINDER')->group(function () {
        Route::put('/reminder/{id}', [ReminderController::class, 'updateReminder']);
    });

    Route::middleware('hasToken:REMINDER_DELETE_REMINDER')->group(function () {
        Route::delete('/reminder/{id}', [ReminderController::class, 'deleteReminder']);
    });

    Route::get('/reminder/all/currentuser', [ReminderController::class, 'getReminderForLoginUser']);

    Route::delete('/reminder/currentuser/{id}', [ReminderController::class, 'deleteReminderCurrentUser']);

    Route::middleware('hasToken:EMAIL_MANAGE_SMTP_SETTINGS')->group(function () {
        Route::put('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'update']);
        Route::delete('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'destroy']);
        Route::get('/emailSMTPSetting/{id}', [EmailSMTPSettingController::class, 'edit']);
    });

    Route::get('/userNotification/notification', [UserNotificationController::class, 'index']);
    Route::get('/userNotification/notifications', [UserNotificationController::class, 'getNotifications']);
    Route::post('/userNotification/MarkAsRead', [UserNotificationController::class, 'markAsRead']);
    Route::post('/UserNotification/MarkAllAsRead', [UserNotificationController::class, 'markAllAsRead']);

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_VIEW_DOCUMENTS,ASSIGNED_DOCUMENTS_SEND_EMAIL']], function () {
        Route::post('/email', [EmailController::class, 'sendEmail']);
    });

    //languages
    Route::middleware('hasToken:SETTING_MANAGE_LANGUAGE')->group(function () {
        Route::post('/languages', [LanguageController::class, 'saveLanguage']);
        Route::delete('/languages/{id}', [LanguageController::class, 'deleteLanguage']);
        Route::get('/languages', [LanguageController::class, 'getLanguages']);
    });

    Route::get('/defaultlanguage', [LanguageController::class, 'defaultlanguage']);
    Route::get('/languageById/{id}', [LanguageController::class, 'getFileContentById']);

    //document-sharable-link-admin
    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_MANAGE_SHARABLE_LINK,ASSIGNED_DOCUMENTS_MANAGE_SHARABLE_LINK']], function () {
        Route::get('/document-sharable-link/{id}', [DocumentSharebaleLinkController::class, 'get']);
        Route::post('/document-sharable-link', [DocumentSharebaleLinkController::class, 'createOrUpdate']);
        Route::delete('/document-sharable-link/{id}', [DocumentSharebaleLinkController::class, 'delete']);
    });

    // page helpers
    Route::group(['middleware' => ['hasToken:PAGE_HELPER_MANAGE_PAGE_HELPER']], function () {
        Route::get('/page-helper', [PageHelperController::class, 'getAll']);
        Route::get('/page-helper/{id}', [PageHelperController::class, 'getById']);
        Route::post('/page-helper/{id}', [PageHelperController::class, 'update']);
    });

    Route::get('/page-helper/{id}/code', [PageHelperController::class, 'getByCode']);

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_VIEW_FILE_REQUEST']], function () {
        Route::get('/file-request', [FileRequestController::class, 'getFileRequests']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_UPDATE_FILE_REQUEST,FILE_REQUEST_VIEW_FILE_REQUEST']], function () {
        Route::get('/file-request/{id}', [FileRequestController::class, 'get']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_CREATE_FILE_REQUEST']], function () {
        Route::post('/file-request', [FileRequestController::class, 'create']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_UPDATE_FILE_REQUEST']], function () {
        Route::put('/file-request/{id}', [FileRequestController::class, 'update']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_DELETE_FILE_REQUEST']], function () {
        Route::delete('/file-request/{id}', [FileRequestController::class, 'delete']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_APPROVE_FILE_REQUEST']], function () {
        Route::post('/file-request-document/document', [FileRequestDocumentController::class, 'approveDocument']);
    });

    Route::group(['middleware' => ['hasToken:FILE_REQUEST_REJECT_FILE_REQUEST']], function () {
        Route::put('/file-request-document/reject', [FileRequestDocumentController::class, 'rejectDocument']);
    });

    Route::get('/file-request-document/{id}', [FileRequestDocumentController::class, 'getFileRequestDocumentData']);
    Route::get('/file-request-document/{id}/download', [FileRequestDocumentController::class, 'downloadDocument']);
    Route::get('/file-request-document/{id}/readText', [FileRequestDocumentController::class, 'readTextDocument']);

    Route::group(['middleware' => ['hasToken:SETTINGS_MANAGE_ALLOW_FILE_EXTENSIONS']], function () {
        Route::put('/file-extensions/{id}', [AllowFileExtensionController::class, 'update']);
    });

    Route::get('/file-extensions/{id}', [AllowFileExtensionController::class, 'get']);

    Route::middleware('hasToken:CLIENTS_MANAGE_CLIENTS')->group(function () {
        Route::post('/clients', [ClientController::class, 'create']);
        Route::get('/clients/{id}', [ClientController::class, 'get']);
        Route::put('/clients/{id}', [ClientController::class, 'update']);
        Route::delete('/clients/{id}', [ClientController::class, 'delete']);
    });

    Route::middleware('hasToken:MANAGE_DOCUMENT_STATUS')->group(function () {
        Route::post('/documentStatus', [DocumentStatusController::class, 'create']);
        Route::get('/document-status/{id}', [DocumentStatusController::class, 'get']);
        Route::put('/document-status/{id}', [DocumentStatusController::class, 'update']);
        Route::delete('/document-status/{id}', [DocumentStatusController::class, 'delete']);
    });

    Route::group(['middleware' => ['hasToken:MANAGE_AI_PROMPT_TEMPLATES']], function () {
        Route::post('/aIPromptTemplate', [AIPromptTemplateController::class, 'create']);
        Route::get('/aIPromptTemplate/{id}', [AIPromptTemplateController::class, 'get']);
        Route::put('/aIPromptTemplate/{id}', [AIPromptTemplateController::class, 'update']);
        Route::delete('/aIPromptTemplate/{id}', [AIPromptTemplateController::class, 'delete']);
    });

    Route::group(['middleware' => ['hasToken:VIEW_AI_GENERATED_DOCUMENTS']], function () {
        Route::get('/stream-document', [OpenAIController::class, 'getOpenAiDocuments']);
        Route::get('/stream-document/{id}', [OpenAIController::class, 'getOpenApiDocumentReponse']);
    });

    Route::group(['middleware' => ['hasToken:GENERATE_AI_DOCUMENTS']], function () {
        Route::post('/stream-document', [OpenAIController::class, 'stream']);
        Route::post('/ai-document', [DocumentController::class, 'saveAIDocument']);
    });

    Route::group(['middleware' => ['hasToken:DELETE_AI_GENERATED_DOCUMENTS']], function () {
        Route::delete('/stream-document/{id}', [OpenAIController::class, 'delete']);
    });

    Route::middleware('hasToken:WORKFLOW_VIEW_WORKFLOW_SETTINGS')->group(function () {
        Route::get('/workflow', [WorkflowController::class, 'getAll']);
    });

    Route::group(['middleware' => ['hasToken:WORKFLOW_ADD_WORKFLOW,WORKFLOW_UPDATE_WORKFLOW']], function () {
        //workflow
        Route::get('/workflow/{id}', [WorkflowController::class, 'get']);
        Route::post('/workflow', [WorkflowController::class, 'create']);
        Route::put('/workflow/{id}', [WorkflowController::class, 'update']);

        //steps
        Route::get('/workflow/{id}/step', [WorkflowStepController::class, 'get']);
        Route::put('/workflowStep/{id}', [WorkflowStepController::class, 'update']);
        Route::post('/workflowStep', [WorkflowStepController::class, 'createWorkFlowStep']);

        //transitions
        Route::get('/workflow/{id}/transitions', [WorkflowTransitionController::class, 'get']);
        Route::put('/workflowTransitions/{id}', [WorkflowTransitionController::class, 'update']);
        Route::post('/workflowTransition', [WorkflowTransitionController::class, 'createWorkFlowTransition']);
    });

    Route::middleware('hasToken:WORKFLOW_DELETE_WORKFLOW')->group(function () {
        Route::delete('/workflow/{id}', [WorkflowController::class, 'delete']);
    });

    Route::middleware('hasToken:WORKFLOW_VIEW_MY_WORKFLOWS')->group(function () {
        Route::get('/my-workflow', [WorkflowController::class, 'getMyWorkflow']);
    });

    Route::group(['middleware' => ['hasToken:ALL_DOCUMENTS_START_WORKFLOW,ASSIGNED_DOCUMENTS_START_WORKFLOW']], function () {
        Route::post('/documentWorkflow', [DocumentWorkflowController::class, 'saveDocumentWorkFlow']);
    });

    Route::middleware('hasToken:WORKFLOW_ALL_CANCEL_WORKFLOW')->group(function () {
        Route::post('/documentWorkflow/{id}/cancel', [DocumentWorkflowController::class, 'cancelWorkflow']);
    });

    Route::middleware('hasToken:WORKFLOW_VIEW_ALL_WORKFLOWS')->group(function () {
        Route::get('/documentWorkflow', [DocumentWorkflowController::class, 'getDocumentWorkFlows']);
    });

    Route::group(['middleware' => ['hasToken:WORKFLOW_VIEW_WORKFLOW_LOGS,ALL_DOCUMENTS_VIEW_DETAIL,ASSIGNED_DOCUMENTS_VIEW_DETAIL']], function () {
        Route::get('/workflow-logs', [WorkflowLogController::class, 'getWorkflowLogs']);
    });
});

Route::get('/file-extensions', [AllowFileExtensionController::class, 'index']);
Route::get('/file-request/{id}/data', [FileRequestController::class, 'getFileRequestData']);
Route::post('/file-request/{id}/verify-password', [FileRequestController::class, 'varifyPassword']);

//upload document for file request
Route::post('/file-request-document/{id}', [FileRequestDocumentController::class, 'saveDocument']);

//document-sharable-link-public
Route::get('/document-sharable-link/{id}/info', [DocumentSharebaleLinkController::class, 'getLinkInfoByCode']);
Route::get('/document-sharable-link/{id}/document', [DocumentSharebaleLinkController::class, 'getDocumentByCode']);
Route::post('/document-sharable-link/{id}/readText', [DocumentController::class, 'readSharedTextDocument']);
Route::get('/document-sharable-link/{id}/download', [DocumentController::class, 'downloadSharedDocument']);
Route::get('/document-sharable-link/{id}/token', [DocumentTokenController::class, 'getSharedDocumentToken']);

Route::get('/i18n/{fileName}', [LanguageController::class, 'downloadFile']);
