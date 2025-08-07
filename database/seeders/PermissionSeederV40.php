<?php

namespace Database\Seeders;

use App\Models\Actions;
use App\Models\AIPromptTemplates;
use App\Models\PageHelper;
use App\Models\Pages;
use App\Models\RoleClaims;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Users;

class PermissionSeederV40 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Users::first();

        $bulkUploadAction = Actions::where('code', '=', 'BULK_DOCUMENT_UPLOAD')->first();
        if ($bulkUploadAction == null) {

            $bulkUploadPage = [
                [
                    'id' => '8384e302-eaf1-4a0b-b293-a921b1e9e36a',
                    'name' => 'BULK_DOCUMENT_UPLOADS',
                    'order' => 4,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $bulkUploadAction =
                [
                    [
                        'id' => 'cb988c3a-7487-4366-9521-c0c5adf9b5a6',
                        'name' => 'BULK_DOCUMENT_UPLOAD',
                        'order' => 1,
                        'pageId' => '8384e302-eaf1-4a0b-b293-a921b1e9e36a',
                        'code' => 'BULK_DOCUMENT_UPLOAD',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                ];

            $bulkUploadRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'cb988c3a-7487-4366-9521-c0c5adf9b5a6',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'BULK_DOCUMENT_UPLOAD',
                ],
            ];

            $bulkUploadPageHelpers =
                [
                    ['id' => '0d3afaea-1984-41f9-a826-fa5b0a9ccad3', 'code' => 'BULK_DOCUMENT_UPLOADS', 'name' => 'Document Bulk Upload', 'description' => "<h3><strong>Bulk Document Uploads</strong></h3><p>Easily upload multiple documents to your system with the following steps:</p><p><strong>1.Category</strong></p><ul><li><strong>Select Category</strong>: Choose a category where your documents will be stored. This helps organize your uploads.</li></ul><p><strong>2.Document Status</strong></p><ul><li>Define the status of each document (e.g., Draft, Final, Archived). This ensures clarity and organization.</li></ul><p><strong>3.Storage</strong></p><ul><li>Select the storage location for your documents:<ul><li><strong>Local</strong>: Save documents to the local storage system.</li></ul></li></ul><p><strong>4.Assign By Roles</strong></p><ul><li><strong>5.Roles</strong>: Assign specific roles to the documents. For example: Manager, Editor, or Viewer.</li><li>This determines which roles have access to the uploaded documents.</li></ul><p><strong>6.Assign By Users</strong></p><ul><li><strong>7.Users</strong>: Assign individual users who can access these documents.</li><li>Select from a list of users in your system.</li></ul><p><strong>8.Document Upload</strong></p><ul><li>Select multiple files to upload from your device.</li><li>Ensure the file extensions are in the allowed list.</li><li>Optionally, rename files before uploading to keep them organized.</li></ul><p><strong>9.Finalize Upload</strong></p><ul><li>After filling out all the required fields, upload the documents.</li><li>The system will automatically assign the selected roles and users to each uploaded file.</li></ul>"],
                ];

            Pages::insert($bulkUploadPage);
            Actions::insert($bulkUploadAction);
            RoleClaims::insert($bulkUploadRoleClaims);
            PageHelper::insert($bulkUploadPageHelpers);
        }

        $aiDocumentPage = Pages::where('name', '=', 'AI_DOCUMENTS')->first();
        if ($aiDocumentPage == null) {
            $pages = [
                [
                    'id' => '637f010e-3397-41a9-903a-21d54db5e49a',
                    'name' => 'AI_DOCUMENTS',
                    'order' => 3,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $aiPageActions =
                [
                    [
                        'id' => 'e3fcd910-3f9b-4035-9bbb-312c5b599d52',
                        'name' => 'GENERATE_AI_DOCUMENTS',
                        'order' => 1,
                        'pageId' => '637f010e-3397-41a9-903a-21d54db5e49a',
                        'code' => 'GENERATE_AI_DOCUMENTS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'fa5b07a4-e8c4-40e2-b5cf-f1a562087783',
                        'name' => 'VIEW_AI_GENERATED_DOCUMENTS',
                        'order' => 2,
                        'pageId' => '637f010e-3397-41a9-903a-21d54db5e49a',
                        'code' => 'VIEW_AI_GENERATED_DOCUMENTS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'b0f2a1c4-3d8e-4b5c-9f6d-7a0e5f3b8c1d',
                        'name' => 'DELETE_AI_GENERATED_DOCUMENTS',
                        'order' => 3,
                        'pageId' => '637f010e-3397-41a9-903a-21d54db5e49a',
                        'code' => 'DELETE_AI_GENERATED_DOCUMENTS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'bc515aea-ef66-4d8d-9cdb-47477cb74145',
                        'name' => 'MANAGE_AI_PROMPT_TEMPLATES',
                        'order' => 4,
                        'pageId' => '637f010e-3397-41a9-903a-21d54db5e49a',
                        'code' => 'MANAGE_AI_PROMPT_TEMPLATES',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => '63355376-2650-4949-9580-fc8c888353f0',
                        'name' => 'Manage Open AI API Key',
                        'order' => 2,
                        'pageId' => '8fbb83d6-9fde-4970-ac80-8e235cab1ff2',
                        'code' => 'SETTINGS_MANAGE_OPEN_AI_API_KEY',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ]
                ];

            $aiRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'e3fcd910-3f9b-4035-9bbb-312c5b599d52',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'GENERATE_AI_DOCUMENTS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'fa5b07a4-e8c4-40e2-b5cf-f1a562087783',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'VIEW_AI_GENERATED_DOCUMENTS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'b0f2a1c4-3d8e-4b5c-9f6d-7a0e5f3b8c1d',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'DELETE_AI_GENERATED_DOCUMENTS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'bc515aea-ef66-4d8d-9cdb-47477cb74145',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'MANAGE_AI_PROMPT_TEMPLATES',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '63355376-2650-4949-9580-fc8c888353f0',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'SETTINGS_MANAGE_OPEN_AI_API_KEY',
                ]
            ];

            $aiPageHelper =
                [
                    ['id' => '49612137-bc17-4b60-b905-3c48734500bd', 'code' => 'AI_DOCUMENT_GENERATOR', 'name' => 'AI Document Generator', 'description' => "<h2>🧠 Using the AI Document Generator</h2><h3>Step-by-Step Instructions</h3><h4>🖊️ 1. <strong>Enter a Prompt</strong></h4><p>Navigate to the section where document creation is available. You will see a prompt input field labeled <strong>\"Generate with AI\"</strong> or similar.</p><blockquote><p>Example Prompt:<br>“Write a GDPR privacy policy for a small e-commerce company.”</p></blockquote><h4>▶️ 2. <strong>Click ‘Generate’</strong></h4><p>Click the <strong>\"Generate\"</strong> or <strong>\"Submit\"</strong> button next to the prompt box. This sends your request to the backend, which calls OpenAI.</p><h4>🔄 3. <strong>Real-Time Streaming</strong></h4><p>You’ll begin to see content populate inside the rich text editor <strong>(CKEditor)</strong> live — no need to refresh or wait for a full response. The AI streams back sentences as it composes.</p><blockquote><p>💡 This process typically starts in 1–2 seconds and streams text smoothly in real-time.</p></blockquote><h4>✍️ 4. <strong>Edit the Content</strong></h4><p>Once the document is generated, you can:</p><ul><li>Edit directly inside the CKEditor</li><li>Apply formatting (headings, lists, links, etc.)</li><li>Save the document into the system or export as needed</li></ul><h2>🧰 Advanced Features</h2><ul><li>✅ <strong>Real-Time Streaming</strong>: Watch content appear as it’s generated.</li><li>🔒 <strong>Secure Access</strong>: Only authenticated users can access the API using Bearer tokens.</li><li>💾 <strong>Auto Save</strong>: Generated content is automatically stored along with the original prompt.</li><li>📝 <strong>Markdown to HTML</strong>: The system parses markdown and renders it as rich text in the editor.</li></ul><h2>❗ Common Issues &amp; Troubleshooting</h2><figure class=\"table\"><table><thead><tr><th>Issue</th><th>Cause</th><th>Solution</th></tr></thead><tbody><tr><td>⚠️ Nothing is generated</td><td>Blank prompt or network error</td><td>Make sure you entered a valid prompt and are connected to the internet</td></tr><tr><td>🔒 419 Error</td><td>Missing CSRF token or unauthorized call</td><td>Ensure you're logged in and the request includes a valid Bearer token</td></tr><tr><td>❌ API Failed</td><td>OpenAI error or rate limit</td><td>Try again in a few minutes or check logs for API key issues</td></tr></tbody></table></figure><h2>📈 Best Practices</h2><ul><li>Use clear and specific prompts to get better results.</li><li>Include document type or target audience in your prompt.<ul><li>✅ “Create an employee NDA agreement for a startup in plain language.”</li></ul></li><li>Edit the AI-generated content before final submission for accuracy.</li></ul><h2>🛡️ Security Notes</h2><ul><li>Your prompt and result are stored securely in the system.</li><li>OpenAI credentials are never exposed to the client.</li><li>Only authenticated users can trigger the AI generation feature.</li></ul><h2>📞 Need Help?</h2><p>If you encounter issues:</p><ul><li>Contact Support via the Help Center</li><li>Check your browser console (F12) for error messages</li><li>Ensure your token is active and valid</li></ul><p>Would you like this delivered as a downloadable <strong>PDF</strong> or <strong>Markdown</strong> file for sharing? I can generate one for you right away.</p>"],
                    ['id' => 'd1ee0a7e-7962-46f5-a784-8be66fb58b51', 'code' => 'AI_DOCUMENTS', 'name' => 'AI Document Lists', 'description' => "<h3>Overview</h3><p>This section allows you to view documents generated using OpenAI's AI. For each document, you can explore the original prompt that was used to generate the content, along with the full AI-generated output. This helps you understand how prompts shape responses and lets you track your creative or work process.</p><h3>🔍 How to Use</h3><h4>1. <strong>Accessing the Document List</strong></h4><ul><li>Navigate to the <strong>Generated Documents</strong> section from the main menu.</li><li>You’ll see a list of all documents generated by AI, including titles and creation dates.</li></ul><h4>2. <strong>Viewing a Document</strong></h4><ul><li>Click on any document in the list to open it.</li><li>You’ll see:<ul><li><strong>Prompt</strong> – The exact input (question or instruction) used to generate the document.</li><li><strong>Output</strong> – The AI-generated text based on the prompt.</li></ul></li></ul><h4>3. <strong>Understanding the Prompt-Output Pair</strong></h4><ul><li>Use this feature to:<ul><li>Learn how different prompts lead to different styles or content.</li><li>Refine your own prompt-writing skills.</li><li>Review previous outputs for reuse or inspiration.</li></ul></li></ul>"],
                    ['id' => 'fa5c186a-ed5d-40b0-858e-34cf03a1866f', 'code' => 'PROMPT_TEMPLATE', 'name' => 'AI Prompt Template', 'description' => "<h2>💡 How Prompt Templates Work</h2><p>Prompt templates make it easy to create AI-generated content quickly and consistently.</p><h3>🧩 What is a Prompt Template?</h3><p>A prompt template is a ready-to-use sentence with placeholders (like **description**) that you can fill in with your own content. The AI then uses your completed prompt to generate a response.</p><h3>✨ How to Use:</h3><ol><li><p><strong>Choose a Template</strong><br>Select from a list of available prompt templates, such as:</p><blockquote><p>Answer this email content: **description**.</p></blockquote></li><li><p><strong>Fill in the Blank</strong><br>After selecting a template, the system will ask you to enter a value for each placeholder (e.g., description).<br>Example:</p><blockquote><p><i>I’m unable to attend the meeting due to a personal emergency.</i></p></blockquote></li><li><p><strong>Generate the Final Prompt</strong><br>The system will automatically replace the placeholder with your input:</p><blockquote><p>Answer this email content: I’m unable to attend the meeting due to a personal emergency.</p></blockquote></li><li><strong>Get Your Result</strong><br>The AI will process the completed prompt and generate the content for you.</li></ol>"],
                ];

            $aiTemplates = [
                ['id' => '0e832c07-8a82-4a5b-b415-cc4b466a9056', 'name' => 'Generate tags and keywords for youtube video', 'description' => 'Generate tags and keywords for youtube video', 'promptInput' => 'Generate tags and keywords about **title** for youtube video.', 'modifiedDate' => '2025-04-24 08:06:48'],
                ['id' => '18849032-284e-4ea5-adaf-35ee52e4ddc4', 'name' => 'Generate testimonial', 'description' => 'Generate testimonial', 'promptInput' => 'Generate testimonial for **subject**. Include details about how it helped you and what you like best about it.', 'modifiedDate' => '2025-04-24 07:57:12'],
                ['id' => '1a4e4a31-f197-4e6f-a58a-e599f216f6ce', 'name' => 'Generate blog post conclusion', 'description' => 'Generate blog post conclusion', 'promptInput' => 'Write blog post conclusion about title: **title**. And the description is **description**.', 'modifiedDate' => '2025-04-24 08:03:29'],
                ['id' => '20804416-cb1b-4016-840d-6f6d625ac210', 'name' => 'Write Problem Agitate Solution', 'description' => 'Write Problem Agitate Solution', 'promptInput' => 'Write Problem-Agitate-Solution copy for the **description**.', 'modifiedDate' => '2025-04-24 07:57:56'],
                ['id' => '30d72e36-1ef7-4ba9-8a8d-db119c013157', 'name' => 'Generate Google ads headline for product.', 'description' => 'Generate Google ads headline for product.', 'promptInput' => 'Write Google ads headline product name: **product name**. Description is **description**. Audience is **audience**.', 'modifiedDate' => '2025-04-24 08:47:44'],
                ['id' => '3b28ed3a-88e3-4d04-8537-039202c28977', 'name' => 'Write me blog section', 'description' => 'Write me blog section', 'promptInput' =>  'Write me blog section about **description**.', 'modifiedDate' => '2025-04-24 07:58:20'],
                ['id' => '3bbe9346-2d34-4f43-8510-ab0f2b290459', 'name' => 'Generate Instagram post caption', 'description' => 'Generate Instagram post caption', 'promptInput' =>  'Write Instagram post caption about **title**.', 'modifiedDate' => '2025-04-24 08:07:26'],
                ['id' => '3bc3216e-f5c2-4e93-ae40-50100b166f65', 'name' => 'Post Generator', 'description' => 'Generator Post using Open AI.', 'promptInput' => 'Write a post about **description**.', 'modifiedDate' => '2025-04-23 13:51:27'],
                ['id' => '6e80ce92-ebad-4fbe-a466-d26273695fc7', 'name' => 'Article Generator', 'description' => 'Instantly create unique articles on any topic. Boost engagement, improve SEO, and save time.', 'promptInput' => 'Generate article about **article title**', 'modifiedDate' => '2025-04-23 13:36:00'],
                ['id' => '783724b2-f4ed-473b-af76-6952724aa880', 'name' => 'Generate instagram hastags.', 'description' => 'Generate instagram hastags.', 'promptInput' => 'Write instagram hastags for **keywords**.',  'modifiedDate' => '2025-04-24 08:07:57'],
                ['id' => '8650d81b-2cf3-4fa3-9123-7426bbbd4d94', 'name' => 'Write product description for Product name', 'description' => 'Write product description for Product name', 'promptInput' => 'Write product description for **product name**.', 'modifiedDate' => '2025-04-24 07:55:55'],
                ['id' => '8985b3bb-c69d-4d3b-a8bc-6baecef2c358', 'name' => 'Generate google ads description for product.', 'description' => 'Generate google ads description for product.', 'promptInput' => 'Write google ads description product name: **product name**. Description is **description**. Audience is **audience**.', 'modifiedDate' => '2025-04-24 08:49:24'],
                ['id' => '8a361cde-138b-4fcd-950b-8e759983a3ac', 'name' => 'Grammar Correction', 'description' => 'Grammar Correction', 'promptInput' => 'Correct the grammar. Text is **description**.', 'modifiedDate' => '2025-04-24 08:55:42'],
                ['id' => '8c288cf3-1ff0-4d40-a98c-2744b954e54f', 'name' => 'Generate pros & cons', 'description' => 'Generate pros & cons', 'promptInput' => 'Generate pros & cons about title:  **title**. Description is **description**.', 'modifiedDate' => '2025-04-24 08:50:36'],
                ['id' => '8c94a143-a07e-4c9d-947c-6a1168c68647', 'name' => 'Email Generator', 'description' =>  'Email Generator', 'promptInput' => 'Write email about title: **subject**, description: **description**.', 'modifiedDate' => '2025-04-24 08:51:56'],
                ['id' => '913a8628-b4f2-41e2-a1aa-f44331afcf00', 'name' => 'Newsletter Generator',  'description' => 'Newsletter Generator', 'promptInput' => 'generate newsletter template about product title: **title**, reason: **subject** description: **description**.', 'modifiedDate' => '2025-04-24 08:53:00'],
                ['id' => '98511559-7b1a-42f6-b924-2430a1bdfd5a', 'name' => 'Generate Facebook ads title', 'description' =>  'Generate Facebook ads title', 'promptInput' => 'Write Facebook ads title about title: **title**. And description is **description**.', 'modifiedDate' => '2025-04-24 08:46:58'],
                ['id' => 'a72ce7d0-720f-48ac-b7f7-7ab25d73a72c', 'name' => 'Summarize Text', 'description' => 'Summarize Text', 'promptInput' => 'Summarize the following text: **text**.', 'modifiedDate' => '2025-04-23 13:57:10'],
                ['id' => 'b9e114c7-a2f1-4777-b43a-e36b1e146dbc', 'name' => 'FAQ Generator', 'description' => 'FAQ Generator', 'promptInput' => 'Answer like faq about subject: **title** Description is **description**.', 'modifiedDate' => '2025-04-24 08:51:34'],
                ['id' => 'c1804540-d86a-48c6-a321-05f13630f262', 'name' => 'Generate website meta description', 'description' => 'Generate website meta description', 'promptInput' =>  'Generate website meta description site name: **title** Description is **description**.', 'modifiedDate' => '2025-04-24 08:51:04'],
                ['id' => 'ca26c30b-e537-4c9f-a4b9-ec4cc7b95a1b', 'name' => 'Rewrite content', 'description' => 'Rewrite content', 'promptInput' =>  'Rewrite content:  **contents**.', 'modifiedDate' => '2025-04-24 08:49:45'],
                ['id' => 'd35d6c5d-9146-464e-bfa9-196f9db0b251', 'name' => 'Generate one paragraph', 'description' => 'Generate one paragraph', 'promptInput' =>  'Generate one paragraph about:  **description**. Keywords are **keywords**.', 'modifiedDate' => '2025-04-24 08:50:11'],
                ['id' => 'd8d81df2-2859-4c6d-99aa-eb6dabb9cc01', 'name' => 'Post Title Generator', 'description' => 'Generator a Post Title from Post Description.', 'promptInput' =>  'Generate Post title about **description**', 'modifiedDate' => '2025-04-23 13:55:24'],
                ['id' => 'ddf9b4d8-1ffc-4582-92f7-6e4adc667c95', 'name' => 'Generate  company social media post', 'description' => 'Generate  company social media post', 'promptInput' =>  'Write in company social media post, company name: **company name**. About: **description**.', 'modifiedDate' => '2025-04-24 08:44:33'],
                ['id' => 'e884ec96-547c-4f81-99e9-40eed842f8b5', 'name' => 'Generate youtube video description', 'description' => 'Generate youtube video description', 'promptInput' =>  'write youtube video description about **title**.', 'modifiedDate' => '2025-04-24 08:05:13'],
                ['id' => 'ea82c689-ad2a-4b54-b11b-4545af7a236d', 'name' => 'Generate YouTube video titles', 'description' => 'Generate YouTube video titles', 'promptInput' =>  'Craft captivating, attention-grabbing video titles about **description** for YouTube rankings.', 'modifiedDate' => '2025-04-24 08:06:08'],
                ['id' => 'f3431223-1eba-4f47-b1f5-8a990a3022af', 'name' => 'Email Answer Generator', 'description' => 'Email Answer Generator', 'promptInput' => 'answer this email content: **description**.', 'modifiedDate' => '2025-04-24 08:52:18'],
                ['id' => 'f7057b73-0db5-4fe6-bc5e-44cb9e1b35e4', 'name' => 'Generate blog post introduction', 'description' => 'Generate blog post introduction', 'promptInput' => 'Write blog post intro about title: **title**. And the description is **description**.', 'modifiedDate' => '2025-04-24 08:02:27'],
                ['id' => 'fd71e2b4-427f-40d9-8ab3-b616fc0cf09b', 'name' => 'Generate Facebook ads text', 'description' => 'Generate Facebook ads text', 'promptInput' => 'Write facebook ads text about title: **title**. And the description is **description**.',  'modifiedDate' => '2025-04-24 08:04:16'],
                ['id' => 'fe9b5264-64a2-4772-a033-00088cf11d07', 'name' => 'Generate blog post idea', 'description' => 'Generate blog post idea', 'promptInput' => 'Write blog post article ideas about **description**.', 'modifiedDate' => '2025-04-24 08:01:51']
            ];

            Pages::insert($pages);
            Actions::insert($aiPageActions);
            RoleClaims::insert($aiRoleClaims);
            PageHelper::insert($aiPageHelper);
            AIPromptTemplates::insert($aiTemplates);
        }

        $clientPage = Pages::where('name', '=', 'CLIENT')->first();
        if ($clientPage == null) {
            $pages = [
                [
                    'id' => '34328287-3a37-4c70-ac61-b291c3ef5ade',
                    'name' => 'CLIENT',
                    'order' => 10,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $clientActions =
                [
                    [
                        'id' => '4cce3cb4-5179-4fc7-b59c-7b15afc747f7',
                        'name' => 'MANAGE_CLIENTS',
                        'order' => 1,
                        'pageId' => '34328287-3a37-4c70-ac61-b291c3ef5ade',
                        'code' => 'CLIENTS_MANAGE_CLIENTS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                ];

            $clientRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => '4cce3cb4-5179-4fc7-b59c-7b15afc747f7',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'CLIENTS_MANAGE_CLIENTS',
                ],
            ];

            Pages::insert($pages);
            Actions::insert($clientActions);
            RoleClaims::insert($clientRoleClaims);
        }

        $documentStatusPage = Pages::where('name', '=', 'DOCUMENT_STATUS')->first();
        if ($documentStatusPage == null) {
            $pages = [
                [
                    'id' => '8740dd7a-7bca-442f-b50f-6cdf0fcaf7bd',
                    'name' => 'DOCUMENT_STATUS',
                    'order' => 10,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $documentStatusActions =
                [
                    [
                        'id' => 'e017d419-8080-4b2d-ac89-4e966182a12f',
                        'name' => 'MANAGE_DOCUMENT_STATUS',
                        'order' => 1,
                        'pageId' => '8740dd7a-7bca-442f-b50f-6cdf0fcaf7bd',
                        'code' => 'MANAGE_DOCUMENT_STATUS',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                ];

            $documentStatusRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'e017d419-8080-4b2d-ac89-4e966182a12f',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'MANAGE_DOCUMENT_STATUS',
                ],
            ];

            $documentStatusPageHelper = [
                'id' => 'dd217b6b-b332-44ef-bc09-2fb68d9b0d79',
                'code' => 'DOCUMENT_STATUS',
                'name' => 'Document Status',
                'description' => "<h3>Document Status</h3><p>Document status is a feature that allows you to manage the lifecycle of your documents. You can set different statuses for your documents, such as:</p><ul><li>Draft</li><li>Final</li><li>Archived</li></ul><p>This helps you keep track of the current state of each document and ensures that only the right people have access to them.</p>"
            ];

            Pages::insert($pages);
            Actions::insert($documentStatusActions);
            RoleClaims::insert($documentStatusRoleClaims);
            PageHelper::insert($documentStatusPageHelper);
        }

        $documentDetailsAction = Actions::where('code', '=', 'ALL_DOCUMENTS_VIEW_DETAIL')->first();
        if ($documentDetailsAction == null) {
            $documentDetailsAction = [
                [
                    'id' => '37db8a21-e552-466d-bcf4-f90f5e4e1008',
                    'name' => 'VIEW_DETAIL',
                    'order' => 9,
                    'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                    'code' => 'ALL_DOCUMENTS_VIEW_DETAIL',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => '8d7e1668-ab2d-4aa5-b8d1-0358906d6995',
                    'name' => 'VIEW_DETAIL',
                    'order' => 9,
                    'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                    'code' => 'ASSIGNED_DOCUMENTS_VIEW_DETAIL',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
            ];

            $documentDetailsRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => '37db8a21-e552-466d-bcf4-f90f5e4e1008',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_VIEW_DETAIL',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '8d7e1668-ab2d-4aa5-b8d1-0358906d6995',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_VIEW_DETAIL',
                ]
            ];

            Actions::insert($documentDetailsAction);
            RoleClaims::insert($documentDetailsRoleClaims);
        }
    }
}
