import { DocumentInfo } from "./document-info";
import { Workflow } from "./workflow";
import { WorkflowTransition } from "./workflow-transition";

export class DocumentWorkflow {
  id?: string;
  documentId: string;
  workflowId: string;
  currentStepId?: string;
  createdDate?: Date; // Timestamp
  document?: DocumentInfo; // Optional reference to Document
  workflow?: Workflow; // Optional reference to Workflow
  documentName?: string;
  workflowName?: string;
  lastTransition?: WorkflowTransition;
  nextTransitions?: WorkflowTransition[] = [];
}
