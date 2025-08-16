
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function atLeastOneRequiredValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    const roleIds = control.get('roleIds')?.value;
    const userIds = control.get('userIds')?.value;

    if ((!roleIds || roleIds?.length === 0) && (!userIds || userIds?.length === 0)) {
      return { atLeastOneRequired: true };
    }
    return null;

    // if (roleIds.length === 0 && userIds.length === 0) {
    //   return { atLeastOneRequired: true };
    // }
    // return null;
  };
}
