import { ResourceParameter } from './resource-parameter';

export class WorkflowsResource extends ResourceParameter {
    workflowName: string = '';
    status: string = '';
    documentName: string = '';
}