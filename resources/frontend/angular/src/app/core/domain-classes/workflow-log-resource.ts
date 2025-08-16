import { ResourceParameter } from './resource-parameter';

export class WorkflowLogResource extends ResourceParameter {
    workflowName: string = '';
    documentName: string = '';
    status: string = '';
    documentId?: string = '';
}
