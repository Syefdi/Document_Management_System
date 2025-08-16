// write function to validate the transition from step and to step are not same
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function fromToStepValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    const fromStepId = control.get('fromStepId')?.value;
    const toStepId = control.get('toStepId')?.value;

    if (fromStepId && toStepId && fromStepId === toStepId) {
      return { sameStep: true };
    }
    return null;
  };
}
