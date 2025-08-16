import { Routes } from '@angular/router';
import { CreateWorkflowComponent } from './create-workflow/create-workflow.component';
import { ManageStepComponent } from './manage-step/manage-step.component';
import { ManageTransitionComponent } from './manage-transition/manage-transition.component';
import { createWorkFlowResolver } from './create-workflow-resolver';
import { AuthGuard } from '@core/security/auth.guard';
export const WORKFLOW_ROUTES: Routes = [
    {
        path: 'manage-steps',
        component: ManageStepComponent,
        canActivate: [AuthGuard],
        data: { claimType: ['WORKFLOW_ADD_WORKFLOW', 'WORKFLOW_UPDATE_WORKFLOW', 'WORKFLOW_DELETE_WORKFLOW'] },

    },
    {
        path: 'manage-transitions',
        component: ManageTransitionComponent,
        canActivate: [AuthGuard],
        data: { claimType: ['WORKFLOW_ADD_WORKFLOW', 'WORKFLOW_UPDATE_WORKFLOW', 'WORKFLOW_DELETE_WORKFLOW'] },
    },
    {
        path: ':id',
        resolve: { workflow: createWorkFlowResolver },
        component: CreateWorkflowComponent,
        canActivate: [AuthGuard],
        data: { claimType: ['WORKFLOW_ADD_WORKFLOW', 'WORKFLOW_UPDATE_WORKFLOW', 'WORKFLOW_DELETE_WORKFLOW'] },
    },
];
