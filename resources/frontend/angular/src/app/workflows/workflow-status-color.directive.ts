import { Directive, ElementRef, Input } from '@angular/core';
import { WorkflowStatus } from '@core/domain-classes/workflow-status.enum';

@Directive({
  selector: '[workflowstatuscolor]',
  standalone: true,
})
export class WorkflowStatusColorDirective {
  @Input() set workflowstatuscolor(status: string) {
    this.setColor(status);
  }
  
  constructor(private el: ElementRef) {}

  private setColor(status: string): void {
    switch (status) {
      case WorkflowStatus.Cancelled:
        this.el.nativeElement.style.color = 'red';
        break;
      case WorkflowStatus.Completed:
        this.el.nativeElement.style.color = 'green';
        break;
      case WorkflowStatus.InProgress:
        this.el.nativeElement.style.color = 'blue';
        break;
      case WorkflowStatus.Initiated:
        this.el.nativeElement.style.color = 'gray';
        break;
      default:
        this.el.nativeElement.style.color = 'black';
        break;
    }
  }
}