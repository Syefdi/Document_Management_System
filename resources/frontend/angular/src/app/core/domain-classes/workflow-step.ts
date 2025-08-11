import { Workflow } from "./workflow";
import { WorkflowStepStatus } from "./workflow-step-status";
import { WorkflowTransition } from "./workflow-transition";

export class WorkflowStep {
  id?: string;
  workflowId: string;
  name: string;
  status?: WorkflowStepStatus;
  isFirst: boolean;
  isLast: boolean;
  workflow?: Workflow; // Optional reference to the Workflow
  outgoingTransitions?: WorkflowTransition[]; // Optional list of outgoing transitions
}
