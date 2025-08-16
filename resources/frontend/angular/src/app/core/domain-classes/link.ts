import { WorkflowTransitionStatus } from "./workflow-transition-status";

export interface Link {
  source: string;
  target: string;
  label: string;
  status: WorkflowTransitionStatus;
}
