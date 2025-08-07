import { CustomColor } from "./custom-color";
import { Link } from "./link";
import { WorkflowTransition } from "./workflow-transition";


export interface VisualWorkflowInstance {
  workflowId: string;
  workflowName: string;
  initiatedBy: string;
  pendingWorkflowTransitions?: WorkflowTransition[];
  workflowDescription?: string;
  documentId: string;
  documentName: string;
  createdDate: Date;
  modifiedDate: Date;
  completedWorkflowTransitions?: WorkflowTransition[]
  nodes?: Node[];
  links?: Link[];
  customColors?: CustomColor[];
  original: string;
  comment?: string;
  createdBy?: string;
  initiatedDate?: Date;
}

export interface MyWorkflow {
  id?: string;
  documentWorkflowId: string;
  workflowId: string;
  workflowName: string;
  initiatedBy: string;
  pendingWorkflowTransitions?: WorkflowTransition[];
  workflowDescription?: string;
  documentId: string;
  documentName: string;
  createdDate: Date;
  updatedAt: Date;
  nodes?: Node[];
  links?: Link[];
  customColors?: CustomColor[];
  nextTransitions?: WorkflowTransition[];
  original: string;
  status?: string;
}

export interface NextTransition {
  transitionId: string;
  comment: string;
  transitionName?: string;
  documentId?: string;
  documentWorkflowId?: string;
}

