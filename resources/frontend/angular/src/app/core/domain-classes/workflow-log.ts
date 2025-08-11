import { User } from "./user";
import { Workflow } from "./workflow";
import { WorkflowTransition } from "./workflow-transition";

export class WorkflowLog {
  id: string;
  documentWorkflowId: string; // Unique identifier for the workflow log
  documentId: string;
  workflowId: string;
  transitionId: string;
  createdBy: string;
  createdDate: Date; // Timestamp
  comment?: string; // Optional comment
  documentName?: string; // Optional name of the document
  document?: Document; // Optional reference to Document
  workflow?: Workflow; // Optional reference to Workflow
  transition?: WorkflowTransition; // Optional reference to the source state
  createdByUser?: User; // Optional reference to the User who executed
}
