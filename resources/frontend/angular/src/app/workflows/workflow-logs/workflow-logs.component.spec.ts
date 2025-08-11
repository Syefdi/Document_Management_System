import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WorkflowLogsComponent } from './workflow-logs.component';

describe('WorkflowLogsComponent', () => {
  let component: WorkflowLogsComponent;
  let fixture: ComponentFixture<WorkflowLogsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [WorkflowLogsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WorkflowLogsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
