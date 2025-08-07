import { WorkflowStep } from "./workflow-step";
import { WorkflowTransition } from "./workflow-transition";

export class Workflow {
    id: string;
    name: string;
    description: string;
    createdBy?: string;
    modifiedBy?: string;
    createdDate?: Date; // Timestamp
    modifiedDate?: Date; // Timestamp
    steps?: WorkflowStep[]; // Optional list of states in the workflow
    transitions?: WorkflowTransition[];
    workflowSteps: WorkflowStep[]; 
    isWorkflowSetup: boolean;
}
