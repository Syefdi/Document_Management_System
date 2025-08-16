import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageAllWorkflowComponent } from './manage-all-workflow.component';

describe('ManageAllWorkflowComponent', () => {
  let component: ManageAllWorkflowComponent;
  let fixture: ComponentFixture<ManageAllWorkflowComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageAllWorkflowComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ManageAllWorkflowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
