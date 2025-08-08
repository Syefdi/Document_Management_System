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

class PermissionSeederV50 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Users::first();
        $workFlowPage = Pages::where('name', '=', 'WORKFLOW_SETTINGS')->first();
        if (!$workFlowPage) {
            $workFlowPages = [
                [
                    'id' => '655f0bcd-676d-49fc-ba30-24c39c853e16',
                    'name' => 'MY_WORKFLOWS',
                    'order' => 9,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ],
                [
                    'id' => '869a8d5e-0430-41f4-94f0-3690895a8942',
                    'name' => 'WORKFLOW_SETTINGS',
                    'order' => 7,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ],
                [
                    'id' => '5a2a2bba-6208-4210-9f71-eb5c215c7d98',
                    'name' => 'ALL_WORKFLOWS',
                    'order' => 7,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ],
                [
                    'id' => 'b2c3d4e5-6f7g-8h9i-0j1k-2l3m4n5o6p7q',
                    'name' => 'WORKFLOW_LOGS',
                    'order' => 7,
                    'isDeleted' => 0,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                ]
            ];

            $workflowSetupAction = [
                // Workflow setup actions
                [
                    'id' => '0f70cc17-26a9-43b1-922e-01fefb248d3c',
                    'name' => 'VIEW_WORKFLOW_LIST',
                    'order' => 1,
                    'pageId' => '869a8d5e-0430-41f4-94f0-3690895a8942',
                    'code' => 'WORKFLOW_VIEW_WORKFLOW_SETTINGS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => '96865813-77f0-40cf-968d-8b9c023d810e',
                    'name' => 'ADD_WORKFLOW',
                    'order' => 2,
                    'pageId' => '869a8d5e-0430-41f4-94f0-3690895a8942',
                    'code' => 'WORKFLOW_ADD_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => 'b1c5f8d2-3e4f-4b0a-9c6d-7e8f9a0b1c2d',
                    'name' => 'UPDATE_WORKFLOW',
                    'order' => 3,
                    'pageId' => '869a8d5e-0430-41f4-94f0-3690895a8942',
                    'code' => 'WORKFLOW_UPDATE_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => 'c2d3e4f5-6a7b-8c9d-0e1f-2a3b4c5d6e7f',
                    'name' => 'DELETE_WORKFLOW',
                    'order' => 4,
                    'pageId' => '869a8d5e-0430-41f4-94f0-3690895a8942',
                    'code' => 'WORKFLOW_DELETE_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                //workflow logs actions
                [
                    'id' => '79ce78a8-0716-4850-a40b-cdc36f3579e4',
                    'name' => 'VIEW_WORKFLOW_LOGS',
                    'order' => 1,
                    'pageId' => 'b2c3d4e5-6f7g-8h9i-0j1k-2l3m4n5o6p7q',
                    'code' => 'WORKFLOW_VIEW_WORKFLOW_LOGS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                // all workflow actions
                [
                    'id' => 'f165f5a2-fe26-490a-91bc-08a736096fed',
                    'name' => 'VIEW_ALL_WORKFLOW',
                    'order' => 1,
                    'pageId' => '5a2a2bba-6208-4210-9f71-eb5c215c7d98',
                    'code' => 'WORKFLOW_VIEW_ALL_WORKFLOWS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => '6b0fe007-1b92-4568-a4b7-6d105eb5c48c',
                    'name' => 'PERFORM_TRANSITION',
                    'order' => 1,
                    'pageId' => '5a2a2bba-6208-4210-9f71-eb5c215c7d98',
                    'code' => 'WORKFLOW_ALL_PERFORM_TRANSITION',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                [
                    'id' => 'd57ff519-1448-4336-8d76-98d43a9ada2c',
                    'name' => 'CANCEL_WORKFLOW',
                    'order' => 1,
                    'pageId' => '5a2a2bba-6208-4210-9f71-eb5c215c7d98',
                    'code' => 'WORKFLOW_ALL_CANCEL_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                // my workflow actions
                [
                    'id' => 'f508f793-5d4c-4e03-889c-2c62b6cf484f',
                    'name' => 'VIEW_MY_WORKFLOW',
                    'order' => 1,
                    'pageId' => '655f0bcd-676d-49fc-ba30-24c39c853e16',
                    'code' => 'WORKFLOW_VIEW_MY_WORKFLOWS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                // all document start workflow action
                [
                    'id' => '707c447d-5e0b-454a-abdf-550d8923eabc',
                    'name' => 'START_WORKFLOW',
                    'order' => 7,
                    'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                    'code' => 'ALL_DOCUMENTS_START_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ],
                // asign document start workflow action
                [
                    'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                    'name' => 'START_WORKFLOW',
                    'order' => 7,
                    'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                    'code' => 'ASSIGNED_DOCUMENTS_START_WORKFLOW',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ]
            ];

            $workflowRoleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => '0f70cc17-26a9-43b1-922e-01fefb248d3c',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_VIEW_WORKFLOW_SETTINGS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '96865813-77f0-40cf-968d-8b9c023d810e',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_ADD_WORKFLOW',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'b1c5f8d2-3e4f-4b0a-9c6d-7e8f9a0b1c2d',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_UPDATE_WORKFLOW',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'c2d3e4f5-6a7b-8c9d-0e1f-2a3b4c5d6e7f',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_DELETE_WORKFLOW',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '79ce78a8-0716-4850-a40b-cdc36f3579e4',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_VIEW_WORKFLOW_LOGS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'f165f5a2-fe26-490a-91bc-08a736096fed',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_VIEW_ALL_WORKFLOWS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'f508f793-5d4c-4e03-889c-2c62b6cf484f',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_VIEW_MY_WORKFLOWS',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '707c447d-5e0b-454a-abdf-550d8923eabc',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_START_WORKFLOW',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_START_WORKFLOW',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => '6b0fe007-1b92-4568-a4b7-6d105eb5c48c',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_ALL_PERFORM_TRANSITION',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'd57ff519-1448-4336-8d76-98d43a9ada2c',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'WORKFLOW_ALL_CANCEL_WORKFLOW',
                ]
            ];

            $workflowPageHelper = [
                ['id' => 'fac2acd8-1cc9-4722-a7e8-b2c297a37b7f', 'code' => 'MANAGE_WORKFLOW', 'name' => 'Manage Workflow', 'description' => "<ul><li><h3><strong>Manage Workflow Overview</strong></h3></li><li>The <strong>Manage Workflow</strong> feature allows users to efficiently create, edit, and customize workflows as needed. This functionality is designed to ensure flexibility and control over workflow management. Here's how it works:</li><li><h4><strong>Creating a Workflow</strong></h4></li><li>If no workflows have been created, users can start by building a new workflow:</li><li><strong>Define Workflow Details</strong>: Provide a unique name and description for the workflow.</li><li><strong>Add Workflow Steps</strong>: Create the necessary steps that outline the workflow process.</li><li><strong>Set Workflow Transitions</strong>: Define the transitions between steps, specifying conditions or rules for movement.</li><li>Once the workflow is created, users can manage and update it as required.</li><li><h4><strong>Editing an Existing Workflow</strong></h4></li><li>For workflows that have already been created, users have the ability to make updates:</li><li><strong>Edit Workflow Name</strong>: Change the name of the workflow to reflect new requirements or corrections.</li><li><strong>Edit Workflow Step Name</strong>: Modify the names of individual steps within the workflow to ensure clarity or adjust for changes.</li><li><strong>Edit Workflow Transition Name</strong>: Update the names or rules for transitions between workflow steps as needed.</li><li><h3>Flexibility in Management</h3></li><li>The <strong>Manage Workflow</strong> feature is versatile, allowing users to either:</li><li><strong>Create a new workflow</strong> if none exist, or</li><li><strong>Edit an existing workflow</strong> to adapt to evolving needs.</li></ul>"],
                ['id' => '955ec3bf-5ec3-40be-b998-542a28e93369', 'code' => 'CURRENT_WORKFLOWS', 'name' => ' Current Workflows', 'description' => "<ul><li><h3>Current Workflow Page Overview</h3></li><li>The <strong>Current Workflow Page</strong> provides users with a personalized view of workflows they have rights to manage or view. This page displays only the workflows associated with the user, ensuring they can easily track and manage their tasks.</li><li><h4><strong>Key Features</strong></h4></li><li><strong>User-Specific Workflow Display</strong>:<ul><li>This page shows <strong>only the workflows</strong> that the user has permission to access and manage.</li><li>The workflows are categorized based on their statuses:<ul><li><strong>Completed</strong>: Workflows that the user has finished or completed steps for.</li><li><strong>Initiated</strong>: Workflows the user has started but are awaiting further progress.</li><li><strong>In Progress</strong>: Workflows where the user is actively involved in ongoing steps.</li><li><strong>Cancelled</strong>: Workflows the user has been part of that were cancelled before completion.</li></ul></li></ul></li><li><strong>Workflow Details in Graphical View</strong>:<ul><li>The workflows are represented graphically to show:<ul><li>The flow of steps and transitions.</li><li><strong>Completed Transitions</strong>: Clearly marked for easy recognition.</li><li><strong>Pending Transitions</strong>: Distinctly highlighted to indicate remaining tasks.</li></ul></li></ul></li><li><strong>Workflow Information Table</strong>:<ul><li>For each workflow, users can view detailed information, including:<ul><li><strong>Workflow Name</strong>: Unique name of the workflow.</li><li><strong>Workflow Status</strong>: Current status (Completed, Initiated, In Progress, Cancelled).</li><li><strong>Initiated By</strong>: The user who initiated the workflow.</li><li><strong>Document Name</strong>: Associated document, if applicable.</li><li><strong>Workflow Step</strong>: The current step(s) the user is involved in.</li><li><strong>Workflow Step Status</strong>: Status of each step (Completed, Pending).</li><li><strong>Performed By</strong>: User(s) responsible for the steps.</li><li><strong>Transition Status</strong>: Whether transitions are completed or pending.</li></ul></li></ul></li><li><strong>Interactive Details</strong>:<ul><li>Users can click on any step or transition to access:<ul><li>Detailed information about that step/transition.</li><li>History and status of the action.</li><li>Relevant timestamps and actions taken.</li></ul></li></ul></li><li><h3>Benefits</h3></li><li>The <strong>Current Workflow Page</strong> is designed for users to have a focused, user-specific view of workflows they have rights to manage. This ensures:</li><li><strong>Personalized Workflow Management</strong>: Only workflows the user is authorized to access are shown.</li><li><strong>Efficient Tracking</strong>: Users can easily track progress of workflows they’re involved in.</li><li><strong>Clear Visibility</strong>: Understanding of the workflow status, transitions, and who is performing each step.</li><li>This page provides a secure and streamlined experience for users to manage their assigned workflows effectively.</li></ul>"],
                ['id' => '99955bd2-fed3-4951-bce7-d0717118e065', 'code' => 'WORKFLOWS', 'name' => 'All Workflows', 'description' => "<ul><li><h3>Workflow List Page Overview</h3></li><li>The <strong>Workflow List Page</strong> provides a complete overview of all workflows, displaying their statuses and details to help users manage and monitor workflows effectively. It combines visual graphs and detailed information to ensure clarity and usability.</li><li><h4><strong>Key Features</strong></h4></li><li><strong>Comprehensive Workflow Display</strong>:<ul><li>All workflows are listed on this page, categorized by their statuses:<ul><li><strong>Completed</strong>: Workflows that have been fully executed.</li><li><strong>Initiated</strong>: Newly started workflows awaiting progress.</li><li><strong>In Progress</strong>: Ongoing workflows with steps and transitions in process.</li><li><strong>Cancelled</strong>: Workflows that were terminated before completion.</li></ul></li></ul></li><li><strong>Workflow Details in Graphical View</strong>:<ul><li>Workflows are visually represented using graphs, showcasing:<ul><li>The structure of steps and transitions.</li><li><strong>Completed Transitions</strong>: Clearly highlighted.</li><li><strong>Pending Transitions</strong>: Distinctly marked.</li></ul></li><li>This graphical format allows users to quickly understand the workflow’s progress and flow.</li></ul></li><li><strong>Workflow Information Table</strong>:<ul><li>Each workflow is accompanied by a table containing detailed information:<ul><li><strong>Workflow Name</strong>: Unique name of the workflow.</li><li><strong>Workflow Status</strong>: Current status of the workflow (Completed, Initiated, In Progress, Cancelled).</li><li><strong>Initiated By</strong>: The user who initiated the workflow.</li><li><strong>Document Name</strong>: The associated document, if applicable.</li><li><strong>Workflow Step</strong>: The current step(s) in the workflow.</li><li><strong>Workflow Step Status</strong>: Status of each step (Completed, Pending).</li><li><strong>Performed By</strong>: The user or team responsible for a specific step.</li><li><strong>Transition Status</strong>: Indicates the progress of transitions (Completed or Pending).</li></ul></li></ul></li><li><strong>Interactive Details</strong>:<ul><li>Users can click on workflow steps or transitions in the graph or table to access:<ul><li>Detailed descriptions.</li><li>Status history.</li><li>Timestamps and related actions.</li></ul></li></ul></li><li><h3>Benefits</h3></li><li>The <strong>Workflow List Page</strong> provides a holistic view of all workflows, their statuses, and detailed progress information. This ensures users can:</li><li>Track and manage all workflows efficiently.</li><li>Monitor progress visually and in detail.</li><li>Quickly identify completed, pending, or cancelled workflows.</li><li>This page is an essential tool for streamlining workflow operations and ensuring process transparency.</li></ul>"],
                ['id' => '66e6f68d-d051-4cc1-9b26-e3fcac4d6e6b', 'code' => 'WORKFLOW_LOGS', 'name' => 'Workflow Logs', 'description' => "<ul><li><h3>Workflow List Page Overview</h3></li><li>The <strong>Workflow List Page</strong> provides a complete overview of all workflows, displaying their statuses and details to help users manage and monitor workflows effectively. It combines visual graphs and detailed information to ensure clarity and usability.</li><li><h4><strong>Key Features</strong></h4></li><li><strong>Comprehensive Workflow Display</strong>:<ul><li>All workflows are listed on this page, categorized by their statuses:<ul><li><strong>Completed</strong>: Workflows that have been fully executed.</li><li><strong>Initiated</strong>: Newly started workflows awaiting progress.</li><li><strong>In Progress</strong>: Ongoing workflows with steps and transitions in process.</li><li><strong>Cancelled</strong>: Workflows that were terminated before completion.</li></ul></li></ul></li><li><strong>Workflow Details in Graphical View</strong>:<ul><li>Workflows are visually represented using graphs, showcasing:<ul><li>The structure of steps and transitions.</li><li><strong>Completed Transitions</strong>: Clearly highlighted.</li><li><strong>Pending Transitions</strong>: Distinctly marked.</li></ul></li><li>This graphical format allows users to quickly understand the workflow’s progress and flow.</li></ul></li><li><strong>Workflow Information Table</strong>:<ul><li>Each workflow is accompanied by a table containing detailed information:<ul><li><strong>Workflow Name</strong>: Unique name of the workflow.</li><li><strong>Workflow Status</strong>: Current status of the workflow (Completed, Initiated, In Progress, Cancelled).</li><li><strong>Initiated By</strong>: The user who initiated the workflow.</li><li><strong>Document Name</strong>: The associated document, if applicable.</li><li><strong>Workflow Step</strong>: The current step(s) in the workflow.</li><li><strong>Workflow Step Status</strong>: Status of each step (Completed, Pending).</li><li><strong>Performed By</strong>: The user or team responsible for a specific step.</li><li><strong>Transition Status</strong>: Indicates the progress of transitions (Completed or Pending).</li></ul></li></ul></li><li><strong>Interactive Details</strong>:<ul><li>Users can click on workflow steps or transitions in the graph or table to access:<ul><li>Detailed descriptions.</li><li>Status history.</li><li>Timestamps and related actions.</li></ul></li></ul></li><li><h3>Benefits</h3></li><li>The <strong>Workflow List Page</strong> provides a holistic view of all workflows, their statuses, and detailed progress information. This ensures users can:</li><li>Track and manage all workflows efficiently.</li><li>Monitor progress visually and in detail.</li><li>Quickly identify completed, pending, or cancelled workflows.</li><li>This page is an essential tool for streamlining workflow operations and ensuring process transparency.</li></ul>"],
            ];

            Pages::insert($workFlowPages);
            Actions::insert($workflowSetupAction);
            RoleClaims::insert($workflowRoleClaims);
            PageHelper::insert($workflowPageHelper);
        }
    }
}
