import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageTransitionComponent } from './manage-transition.component';

describe('ManageTransitionComponent', () => {
  let component: ManageTransitionComponent;
  let fixture: ComponentFixture<ManageTransitionComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageTransitionComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ManageTransitionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
