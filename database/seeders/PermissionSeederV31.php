<?php

namespace Database\Seeders;

use App\Models\Actions;
use App\Models\AllowFileExtensions;
use App\Models\PageHelper;
use App\Models\Pages;
use App\Models\RoleClaims;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Users;

class PermissionSeederV31 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $action = Actions::where('code', '=', 'ALL_DOCUMENTS_UPLOAD_NEW_VERSION')->first();

        if ($action == null) {

            $user = Users::first();

            $pages = [
                [
                    'id' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                    'name' => 'FILE_REQUEST',
                    'order' => 8,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $actions =
                [
                    [
                        'id' => '57f0b2ef-eeba-44a6-bd88-458003f013ef',
                        'name' => 'Upload New Version',
                        'order' => 4,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_UPLOAD_NEW_VERSION',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '229150ef-9007-4c62-9276-13dd18294370',
                        'name' => 'Restore Version',
                        'order' => 4,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_RESTORE_VERSION',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'b36cf0a4-ad53-4938-aac5-fb7fbfc2cfcf',
                        'name' => 'Restore Version',
                        'order' => 4,
                        'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                        'code' => 'ASSIGNED_DOCUMENTS_RESTORE_VERSION',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '165505b2-ad31-42c7-aafe-f66f291cb5a9',
                        'name' => 'Manage Comment',
                        'order' => 4,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_MANAGE_COMMENT',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '44ecbcaf-6d4a-4fc2-911c-e96be65bffb2',
                        'name' => 'Manage Comment',
                        'order' => 4,
                        'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                        'code' => 'ASSIGNED_DOCUMENTS_MANAGE_COMMENT',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'c6e2e9f8-1ee4-4c1d-abd1-721ff604c8b8',
                        'name' => 'Add Reminder',
                        'order' => 4,
                        'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                        'code' => 'ASSIGNED_DOCUMENTS_ADD_REMINDER',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '26e383c9-7f7f-4ed0-b78d-a2941f5b4fe7',
                        'name' => 'Add Reminder',
                        'order' => 4,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_ADD_REMINDER',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'c18e4105-e9d7-4c5d-b396-a2854bcb8e21',
                        'name' => 'View Version History',
                        'order' => 4,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_VIEW_VERSION_HISTORY',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'a5b485ac-8c7b-4a4f-a62d-6f839d77e91f',
                        'name' => 'View Version History',
                        'order' => 4,
                        'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                        'code' => 'ASSIGNED_DOCUMENTS_VIEW_VERSION_HISTORY',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '8e3fbe21-0225-44e2-a537-bb50ddffb95c',
                        'name' => 'MANAGE_ALLOW_FILE_EXTENSIONS',
                        'order' => 4,
                        'pageId' => '8fbb83d6-9fde-4970-ac80-8e235cab1ff2',
                        'code' => 'SETTINGS_MANAGE_ALLOW_FILE_EXTENSIONS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '7562978b-155a-4fb1-bc3f-6153f62ed565',
                        'name' => 'VIEW_FILE_REQUEST',
                        'order' => 1,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_VIEW_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '61de0ba3-f41f-4ca8-9af6-ec8dc456c16b',
                        'name' => 'CREATE_FILE_REQUEST',
                        'order' => 2,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_CREATE_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '2e71e9d6-2302-44d8-b0f6-747b98d89125',
                        'name' => 'UPDATE_FILE_REQUEST',
                        'order' => 3,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_UPDATE_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '0478e8b6-7d52-4c26-99e1-657a1c703e8b',
                        'name' => 'DELETE_FILE_REQUEST',
                        'order' => 4,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_DELETE_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'b4d722d6-755c-4be4-8f0d-2283c9394e18',
                        'name' => 'APPROVE_FILE_REQUEST',
                        'order' => 5,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_APPROVE_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '1ae728c8-58df-4e9f-b284-132dc3c8ff89',
                        'name' => 'REJECT_FILE_REQUEST',
                        'order' => 6,
                        'pageId' => '55e8aeb6-8a97-40f7-acf2-9a028f615ddb',
                        'code' => 'FILE_REQUEST_REJECT_FILE_REQUEST',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                ];

            $roleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => '57f0b2ef-eeba-44a6-bd88-458003f013ef',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_UPLOAD_NEW_VERSION',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '229150ef-9007-4c62-9276-13dd18294370',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_RESTORE_VERSION'
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'b36cf0a4-ad53-4938-aac5-fb7fbfc2cfcf',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_RESTORE_VERSION',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '165505b2-ad31-42c7-aafe-f66f291cb5a9',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_MANAGE_COMMENT',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '44ecbcaf-6d4a-4fc2-911c-e96be65bffb2',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_MANAGE_COMMENT'
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'c6e2e9f8-1ee4-4c1d-abd1-721ff604c8b8',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_ADD_REMINDER',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '26e383c9-7f7f-4ed0-b78d-a2941f5b4fe7',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_ADD_REMINDER',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'c18e4105-e9d7-4c5d-b396-a2854bcb8e21',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_VIEW_VERSION_HISTORY',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'a5b485ac-8c7b-4a4f-a62d-6f839d77e91f',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_VIEW_VERSION_HISTORY',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '8e3fbe21-0225-44e2-a537-bb50ddffb95c',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'SETTINGS_MANAGE_ALLOW_FILE_EXTENSIONS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '7562978b-155a-4fb1-bc3f-6153f62ed565',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_VIEW_FILE_REQUEST',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '61de0ba3-f41f-4ca8-9af6-ec8dc456c16b',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_CREATE_FILE_REQUEST',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '2e71e9d6-2302-44d8-b0f6-747b98d89125',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_UPDATE_FILE_REQUEST',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '0478e8b6-7d52-4c26-99e1-657a1c703e8b',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_DELETE_FILE_REQUEST',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'b4d722d6-755c-4be4-8f0d-2283c9394e18',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_APPROVE_FILE_REQUEST',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '1ae728c8-58df-4e9f-b284-132dc3c8ff89',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'FILE_REQUEST_REJECT_FILE_REQUEST',
                ],

            ];

            $updatedPages =  collect($pages)->map(function ($item, $key) {
                $item['createdDate'] = Carbon::now();
                $item['modifiedDate'] = Carbon::now();
                return $item;
            });

            $updatedActions =  collect($actions)->map(function ($item, $key) {
                $item['createdDate'] = Carbon::now();
                $item['modifiedDate'] = Carbon::now();
                return $item;
            });

            Pages::insert($updatedPages->toArray());
            Actions::insert($updatedActions->toArray());
            RoleClaims::insert($roleClaims);
        }

        $fileExtension = AllowFileExtensions::first();
        if ($fileExtension == null) {
            $extentions =
                [
                    [
                        'id' => '64dac07d-9072-4661-b537-053a09d42d6e',
                        'extensions' => 'doc,docx,ppt,pptx,xls,xlsx,csv',
                        'fileType' => 0,
                    ],
                    [
                        'id' => '3257c50c-a128-4c98-8809-cc2564b7db2a',
                        'extensions' => 'pdf',
                        'fileType' => 1,
                    ],

                    [
                        'id' => '0c0be0a9-0a4e-4f05-8742-3a5d6d74acf0',
                        'extensions' => 'png,jpg,jpge,gif,bmp,tiff,tif,svg,webp,ico,heif,heic,avif,apng,jfif,pjpeg,pjp,svgz,wmf,emf,djv,djvu,eps,ps,ai,indd,idml,psd,tga,dds',
                        'fileType' => 2,
                    ],
                    [
                        'id' => '13a28d05-d6be-4e6b-87fe-b784642e2a95',
                        'extensions' => 'txt',
                        'fileType' => 3,
                    ],
                    [
                        'id' => '9eaf6b33-0cef-45a4-bf92-7c525e2ed536',
                        'extensions' => '3gp,aa,aac,aax,act,aiff,alac,amr,ape,au,awb,dss,dvf,flac,gsm,iklx,ivs,m4a,m4b,m4p,mmf,mp3,mpc,msv,nmf,ogg,oga,mogg,opus,org,ra,rm,raw,rf64,sln,tta,voc,vox,wav,wma,wv',
                        'fileType' => 4,
                    ],

                    [
                        'id' => 'cb1612ef-8e3c-4823-af2b-469f4b0010b8',
                        'extensions' => 'webm,flv,vob,ogv,ogg,drc,avi,mts,m2ts,wmv,yuv,viv,mp4,m4p,3pg,f4v,f4a',
                        'fileType' => 5,
                    ],
                    [
                        'id' => 'ab5db62f-1fc7-49ed-895f-6ac4be6db33a',
                        'extensions' => 'zip',
                        'fileType' => 6,
                    ]
                ];

            AllowFileExtensions::insert($extentions);
        }

        $pageHelper = PageHelper::where('code', '=', 'MANAGE_FILE_REQUEST')->first();
        if ($pageHelper == null) {
            $pageHelpers =
                [
                    ['id' => 'd2abfc80-7dfb-49b6-bccf-44d75844f098', 'code' => 'MANAGE_FILE_REQUEST', 'name' => 'Manage File Request', 'description' => "<h2>File Request Functionality</h2><p>The <strong>File Request</strong> feature simplifies document collection by allowing you to generate unique links, share them with users, and review uploaded documents. Here's how it works:</p><h2>Key Features:</h2><p><strong>1.Generate Link</strong>:</p><ul><li>Create a unique link for a file request.</li><li>Share this link with users to allow them to upload the required documents.</li></ul><p><strong>2.Upload Documents</strong>:</p><p>Users can upload documents directly via the link you provide.</p><p>You can set the following parameters when creating a request:</p><p><strong>Maximum File Size Upload</strong>: Specify the largest file size allowed per upload.</p><p><strong>Maximum Document Upload</strong>: Limit the number of documents a user can upload.</p><ul><li><strong>Allowed File Extensions</strong>: Restrict uploads to specific file types (e.g., PDF, DOCX, JPG).</li></ul><p><strong>3.Review and Manage Requests</strong>:</p><ul><li>View all submissions on the <strong>File Request List</strong> page.</li><li>Approve or reject uploaded documents as necessary.</li></ul><p><strong>4.Request Data List</strong>:<br>Each file request includes the following details:</p><ul><li><strong>Subject</strong>: The purpose or title of the request.</li><li><strong>Email</strong>: The email address associated with the request.</li><li><strong>Maximum File Size Upload</strong>: The size limit for uploaded files.</li><li><strong>Maximum Document Upload</strong>: The number of documents users can upload.</li><li><strong>Allowed File Extensions</strong>: The types of files users can upload.</li><li><strong>Status</strong>: The current status of the request (e.g., Pending, Approved, Rejected).</li><li><strong>Created By</strong>: The user who created the request.</li><li><strong>Created Date</strong>: The date the request was created.</li><li><strong>Link Expiration</strong>: The date the link will no longer be valid.</li></ul><p><strong>5.Manage Requests</strong>:<br>For each file request, you can:</p><ul><li><strong>Edit</strong>: Update the details of the request, such as file size, document limits, or expiration date.</li><li><strong>Delete</strong>: Remove the request entirely.</li><li><strong>Copy Link</strong>: Copy the link to share it with others.</li></ul><h2>How It Works:</h2><h3>1. Creating a File Request:</h3><ul><li>Navigate to the <strong>File Request</strong> page and click Create New Request. </li><li>Enter details like the subject, allowed file extensions, and upload limits.</li><li>Generate the link and share it with the intended user.</li></ul><h3>2. Uploading Documents:</h3><ul><li>The user clicks the link and uploads their documents according to the criteria you set.</li></ul><h3>3. Reviewing Submissions:</h3><ul><li>Go to the <strong>File Request List</strong> page to view submitted documents.</li><li>Approve or reject submissions as required.</li></ul><h3>4. Managing Links:</h3><ul><li>Use the <strong>Edit</strong> or <strong>Delete</strong> options to modify or remove requests.</li><li>Copy the link anytime for reuse or sharing.</li></ul>"],
                    ['id' => '350137e8-91d3-4e53-a621-1fae3fb680eb', 'code' => 'FILE_REQUEST_UPLOADED_DOCUMENTS', 'name' => 'File Request Documents', 'description' => "<h2>File Request Uploaded Documents</h2><p>The <strong>File Request Uploaded Documents</strong> feature allows you to manage the documents submitted through your file request link. You can review, approve, or reject uploaded files and provide feedback or reasons for rejection.</p><h2>Key Features:</h2><p><strong>1.View Uploaded Documents</strong>:</p><ul><li>Access all documents submitted via the file request link.</li><li>See details such as:<ul><li>File Name</li><li>Upload Date</li><li>Document Status</li><li>Reason</li></ul></li></ul><p><strong>2.Approve Documents</strong>:</p><ul><li>Mark documents as <strong>Approved</strong> if they meet your requirements.</li><li>Approved documents will be saved and marked as finalized.</li></ul><p><strong>3.Reject Documents</strong>:</p><ul><li>Reject documents that do not meet the criteria or need corrections.</li><li>When rejecting a document:<ul><li>Add a <strong>Comment</strong> to explain the reason for rejection.</li><li>This ensures users understand what needs to be corrected or resubmitted.</li></ul></li></ul><p><strong>4.Document Preview</strong>:</p><ul><li>View uploaded documents directly before approving or rejecting them.</li><li>Supports previewing common file types such as PDF, DOCX, JPG, and PNG.</li></ul><p><strong>5.Status Tracking</strong>:</p><ul><li>Each document will have a status indicator:<ul><li><strong>Pending</strong>: Awaiting review.</li><li><strong>Approved</strong>: Accepted and finalized.</li><li><strong>Rejected</strong>: Requires resubmission with a reason provided.</li></ul></li></ul><h2>How It Works:</h2><h3>1. Viewing Uploaded Documents:</h3><ul><li>Go to the <strong>File Request Uploaded Documents</strong> page.</li><li>Select the relevant file request from the list.</li><li>All submitted documents for that request will be displayed.</li></ul><h3>2. Approving Documents:</h3><ul><li>Click on the document you want to approve.</li><li>Review the document using the preview feature.</li><li>If the document meets your requirements, click <strong>Approve</strong>.</li><li>The status will change to <strong>Approved</strong>.</li></ul><h3>3. Rejecting Documents:</h3><ul><li>Click on the document you want to reject.</li><li>Use the preview feature to review the document.</li><li>If the document does not meet the requirements:<ul><li>Click <strong>Reject</strong>.</li><li>Enter a <strong>Reason for Rejection</strong> in the comment box (e.g., Incorrect file format or Incomplete information).</li><li>Save the rejection and notify the user to resubmit.</li></ul></li></ul><h3>4. Adding Comments for Rejected Documents:</h3><ul><li>When rejecting a document, always provide a clear and actionable comment.</li><li>Examples of comments:<ul><li>Please upload a file in PDF format.</li><li>The document is missing required signatures.</li><li>File size exceeds the maximum limit; please compress and reupload.</li></ul></li></ul><h2>Benefits:</h2><ul><li><strong>Efficient Review</strong>: Quickly review and take action on uploaded documents.</li><li><strong>Clear Communication</strong>: Provide feedback for rejected documents, ensuring users know what to fix.</li><li><strong>Organized Workflow</strong>: Keep track of document statuses with easy-to-use status indicators.</li></ul><p>This feature ensures a smooth and transparent document review process for both you and the users.</p>"],
                    ['id' => '5475c0fb-5a9e-44e1-b628-6757d6865d2a', 'code' => 'MANAGE_ALLOW_FILE_EXTENSION', 'name' => 'Manage File Extensions', 'description' => "<p><strong>Manage Allowed File Extensions</strong></p><p>This functionality allows users to control which file types are permitted for upload in the application. Users can easily configure allowed file extensions by selecting the desired file types and specifying their extensions in a provided configuration interface. Here's how it works:</p><ol><li><strong>Select File Types</strong>: Users can choose from a predefined list of file types (e.g., images, documents, videos) or manually add custom types.</li><li><strong>Add Extensions</strong>: For each file type, users can specify the associated file extensions (e.g., .jpg, .pdf, .mp4).</li><li><strong>Apply Changes</strong>: Once configured, the application will enforce these rules, ensuring only the specified file types can be uploaded.</li><li><strong>Easy Management</strong>: Users can modify, add, or remove allowed extensions anytime, making the system flexible and easy to update.</li></ol><p>This functionality simplifies file type management and ensures compliance with application requirements or security policies.</p>"],
                    ['id' => '45c53b1a-a865-4c22-b56a-9f5e6cf83528', 'code' => 'CLIENTS', 'name' => 'Clients', 'description' => "<p>The <strong>Clients</strong> section helps you manage and view all your clients in one place. Here’s what you can do:</p><p><strong>1.Clients List</strong></p><ul><li>A list of all your clients is displayed with the following details:</li></ul><p><strong>Action</strong>: Options to edit or delete client information.</p><p><strong>Company/Person Name</strong>: The name of the company or individual client.</p><p><strong>Contact Person</strong>: The primary contact person for the client.</p><p><strong>Email</strong>: The email address of the client for communication.</p><p><strong>Mobile Number</strong>: The mobile number of the client for easy contact.</p><p><strong>2.Add Client</strong></p><ul><li>Click the <strong>Add Client</strong> button to create a new client.</li><li>Fill in details like the company or person name, contact person, email, and mobile number.</li><li>Save the new client, and it will be added to the clients list.</li></ul>"],
                    ['id' => '647244ec-b5b2-4fbf-9c93-6133cb252a40', 'code' => 'MANAGE_CLIENT', 'name' => 'Manage Client', 'description' => "<p>The <strong>Manage Client</strong> feature makes it easy to add new clients or edit existing client details. Here’s how you can use it:</p><h4><strong>Add New Client</strong></h4><p>1.Click the <strong>Add Client</strong> button.</p><p>2.A form will appear where you can enter the following details:</p><p><strong>Company/Person Name</strong>: Enter the name of the company or individual client.</p><p><strong>Contact Person</strong>: Provide the name of the main contact person.</p><p><strong>Email</strong>: Enter the client’s email address.</p><p><strong>Mobile Number</strong>: Add the client’s mobile number for quick contact.</p><p>3.Once all the details are filled in, click the <strong>Save</strong> button to add the new client to the list.</p><p>4.The newly added client will now appear in the <strong>Clients List</strong>.</p><h4><strong>Edit Existing Client</strong></h4><p>1.In the <strong>Clients List</strong>, locate the client whose details you want to edit.</p><p>2.Click the <strong>Edit</strong> button in the <strong>Action</strong> column.</p><p>3.A form will open, pre-filled with the client’s existing details.</p><p>4.Update any necessary fields, such as:</p><p>Correcting the email address or phone number.</p><p>Changing the contact person or company name.</p><p>5.After making the changes, click the <strong>Save</strong> button to update the client’s information.</p><p>6.The changes will reflect immediately in the <strong>Clients List</strong>.</p>"]
                ];
            PageHelper::insert($pageHelpers);
        }
    }
}
