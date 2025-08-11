import { inject, Pipe, PipeTransform } from '@angular/core';
import { WorkflowStatus } from '@core/domain-classes/workflow-status.enum';
import { TranslationService } from '@core/services/translation.service';

@Pipe({
    name: 'workflowStatus',
    standalone: true
})

export class WorkflowStatusPipe implements PipeTransform {
    translationService = inject(TranslationService);
    transform(value: any, ...args: any[]): any {
        if (value === WorkflowStatus.Cancelled) {
            return this.translationService.getValue('CANCELLED');
        }
        else if (value === WorkflowStatus.Completed) {
            return this.translationService.getValue('COMPLETED');
        }
        else if (value === WorkflowStatus.InProgress) {
            return this.translationService.getValue('IN_PROGRESS');
        }
        else if (value === WorkflowStatus.Initiated) {
            return this.translationService.getValue('INITIATED');
        }
    }
}
