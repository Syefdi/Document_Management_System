import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RackListComponent } from './rack-list/rack-list.component';
import { ManageRackComponent } from './manage-rack/manage-rack.component';
import { AuthGuard } from '@core/security/auth.guard';
import { RackResolver } from './rack.resolver';

const routes: Routes = [
  {
    path: '',
    component: RackListComponent,
    canActivate: [AuthGuard],
    data: { claimType: 'RACK_MANAGE_RACKS' }
  },
  {
    path: 'manage',
    component: ManageRackComponent,
    canActivate: [AuthGuard],
    data: { claimType: 'RACK_MANAGE_RACKS' }
  },
  {
    path: 'manage/:id',
    component: ManageRackComponent,
    canActivate: [AuthGuard],
    resolve: { rack: RackResolver },
    data: { claimType: 'RACK_MANAGE_RACKS' }
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RackRoutingModule { }