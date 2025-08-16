import { Directive, ElementRef, Input, OnChanges, Renderer2, SimpleChanges } from '@angular/core';

@Directive({
  selector: '[documentWorkflowStatusColor]'
})
export class DocumentWorkflowStatusColorDirective implements OnChanges {
  @Input('documentWorkflowStatusColor') status: string;

  constructor(private el: ElementRef, private renderer: Renderer2) {}

  ngOnChanges(changes: SimpleChanges): void {
  if (changes['status']) {
      this.updateColor();
    }
  }

  private updateColor(): void {
    const statusText = this.status ? this.status.toLowerCase() : '';
    let className = 'badge-info';

    if (statusText === 'draft' || statusText === 'pending') {
      className = 'badge-secondary';
    } else if (statusText === 'in review') {
      className = 'badge-warning';
    } else if (statusText === 'approved' || statusText === 'completed') {
      className = 'badge-success';
    } else if (statusText === 'reject' || statusText === 'rejected') {
      className = 'badge-danger';
    }

    const classesToRemove = ['badge-info', 'badge-warning', 'badge-success', 'badge-danger', 'badge-secondary'];
    classesToRemove.forEach(c => this.renderer.removeClass(this.el.nativeElement, c));

    this.renderer.addClass(this.el.nativeElement, 'badge');
    this.renderer.addClass(this.el.nativeElement, className);
  }
}
