import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocumentWorkflowDialogComponent } from './document-workflow-dialog.component';

describe('DocumentWorkflowDialogComponent', () => {
  let component: DocumentWorkflowDialogComponent;
  let fixture: ComponentFixture<DocumentWorkflowDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DocumentWorkflowDialogComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocumentWorkflowDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
