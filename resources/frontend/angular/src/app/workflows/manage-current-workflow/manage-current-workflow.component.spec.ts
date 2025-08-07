import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageCurrentWorkflowComponent } from './manage-current-workflow.component';

describe('ManageCurrentWorkflowComponent', () => {
  let component: ManageCurrentWorkflowComponent;
  let fixture: ComponentFixture<ManageCurrentWorkflowComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageCurrentWorkflowComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ManageCurrentWorkflowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
