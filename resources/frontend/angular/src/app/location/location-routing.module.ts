import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuard } from '@core/security/auth.guard';
import { LocationListComponent } from './location-list/location-list.component';
import { ManageLocationComponent } from './manage-location/manage-location.component';
import { LocationResolver } from './location.resolver';

const routes: Routes = [
  {
    path: '',
    component: LocationListComponent,
    data: { claimType: 'LOCATION_MANAGE_LOCATIONS' },
    canActivate: [AuthGuard],
  },
  {
    path: 'manage/:id',
    component: ManageLocationComponent,
    resolve: { location: LocationResolver },
    data: { claimType: 'LOCATION_MANAGE_LOCATIONS' },
    canActivate: [AuthGuard],
  },
  {
    path: 'manage',
    component: ManageLocationComponent,
    data: { claimType: 'LOCATION_MANAGE_LOCATIONS' },
    canActivate: [AuthGuard],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LocationRoutingModule { }