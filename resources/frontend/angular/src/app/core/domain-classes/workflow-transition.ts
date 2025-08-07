import { User } from "./user";
import { Workflow } from "./workflow";
import { WorkflowStep } from "./workflow-step";
import { WorkflowStepStatus } from "./workflow-step-status";
import { WorkflowTransitionRole } from "./workflow-transition-role";
import { WorkflowTransitionUser } from "./workflow-transition-user";

export interface WorkflowTransition {
  id: string;
  workflowId: string;
  fromStepId: string; // Source state ID
  toStepId: string; // Target state ID
  name: string;
  workflow?: Workflow; // Optional reference to the Workflow
  fromStep?: WorkflowStep; // Optional reference to the source state
  toStep?: WorkflowStep; // Optional reference to the target state
  condition: string;
  isFirstTransaction: boolean;
  status?: WorkflowStepStatus;
  fromStepName?: string;
  toStepName?: string;
  assignRoles?: string;
  assignUsers?: string;
  completedAt?: Date;
  createdAt?: Date;
  user?: User;
  minutes?: number;
  days?: number;
  hours?: number;
  workflowTransitionRoles?: WorkflowTransitionRole[];
  workflowTransitionUsers?: WorkflowTransitionUser[];
  color?: string;
  roleIds?: string[];
  userIds?: string[];
  orderNo?: number;
  fromToStepName?: string;
  comment?: string;
  createdDate?: Date;
  createdBy?: string; 
}
