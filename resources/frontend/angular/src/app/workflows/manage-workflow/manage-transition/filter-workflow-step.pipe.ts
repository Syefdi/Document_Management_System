import { Pipe, PipeTransform } from '@angular/core';
import { WorkflowStep } from '@core/domain-classes/workflow-step';

@Pipe({
  name: 'filter_workflow_step',
  standalone: true,
})

export class FilterWorkflowStepPipe implements PipeTransform {
  transform(value: WorkflowStep[], formArrayValue: any, arrayIndex: number): any {
    if (arrayIndex === 0 || (formArrayValue.length > 0 && formArrayValue[0]?.id)) {
      return value;
    }

    const forArrayStepId = [];
    formArrayValue.forEach((step: any, index: number) => {
      if (arrayIndex > index) {
        if (step.fromStepId && !forArrayStepId.find((id) => id === step.fromStepId)) {
          forArrayStepId.push(step.fromStepId);
        }
        if (step.toStepId && !forArrayStepId.find((id) => id === step.toStepId)) {
          forArrayStepId.push(step.toStepId);
        }
      }
      // if (arrayIndex == index && step.fromStepId && !forArrayStepId.find((id) => id === step.fromStepId)) {
      //   forArrayStepId.push(step.fromStepId);
      // }
    });
    // Check forArrayStepId array value exists in the value
    return value.filter((step) => forArrayStepId.find((id) => id === step.id));

  }
}
