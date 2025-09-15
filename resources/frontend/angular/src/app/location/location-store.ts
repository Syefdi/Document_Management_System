import { inject } from '@angular/core';
import { tapResponse } from '@ngrx/operators';
import {
  patchState,
  signalStore,
  withComputed,
  withHooks,
  withMethods,
  withState,
} from '@ngrx/signals';
import { rxMethod } from '@ngrx/signals/rxjs-interop';
import { ToastrService } from 'ngx-toastr';
import { debounceTime, distinctUntilChanged, pipe, switchMap, tap } from 'rxjs';
import { CommonError } from '@core/error-handler/common-error';
import { TranslationService } from '@core/services/translation.service';
import { SecurityService } from '@core/security/security.service';
import { LocationService } from './location.service';
import { Location } from '@core/domain-classes/location';

type LocationState = {
  locations: Location[];
  location: Location;
  isLoading: boolean;
  loadList: boolean;
  isAddUpdate: boolean;
  commonError: CommonError;
};

export const initialLocationState: LocationState = {
  locations: [],
  location: null,
  isLoading: false,
  loadList: false,
  isAddUpdate: false,
  commonError: null,
};

export const LocationStore = signalStore(
  { providedIn: 'root' },
  withState(initialLocationState),
  withComputed(({}) => ({})),
  withMethods(
    (
      store,
      locationService = inject(LocationService),
      toastrService = inject(ToastrService),
      translationService = inject(TranslationService)
    ) => ({
      loadLocations: rxMethod<void>(
        pipe(
          debounceTime(300),
          tap(() => patchState(store, { isLoading: true })),
          switchMap(() =>
            locationService.getLocations().pipe(
              tapResponse({
                next: (locations: Location[]) => {
                  patchState(store, {
                    locations: locations,
                    isLoading: false,
                  });
                },
                error: (err: CommonError) => {
                  patchState(store, { isLoading: false, commonError: err });
                },
              })
            )
          )
        )
      ),
      deleteLocationById: rxMethod<string>(
        pipe(
          distinctUntilChanged(),
          tap(() => patchState(store, { isLoading: true })),
          switchMap((locationId: string) =>
            locationService.deleteLocation(locationId).pipe(
              tapResponse({
                next: () => {
                  const locations = store.locations().filter((c) => c.id !== locationId);
                  patchState(store, {
                    locations: locations,
                    isLoading: false,
                  });
                  toastrService.success(translationService.getValue('LOCATION_DELETED_SUCCESSFULLY'));
                },
                error: (err: CommonError) => {
                  patchState(store, { isLoading: false, commonError: err });
                },
              })
            )
          )
        )
      ),
      addUpdateLocation: rxMethod<Location>(
        pipe(
          distinctUntilChanged(),
          tap(() => patchState(store, { isLoading: true })),
          switchMap((location: Location) => {
            if (location.id) {
              return locationService.updateLocation(location).pipe(
                tapResponse({
                  next: (updatedLocation: Location) => {
                    const locations = store.locations().map((c) =>
                      c.id === updatedLocation.id ? updatedLocation : c
                    );
                    patchState(store, {
                      locations: locations,
                      isLoading: false,
                      isAddUpdate: true,
                    });
                    toastrService.success(translationService.getValue('LOCATION_UPDATED_SUCCESSFULLY'));
                  },
                  error: (err: CommonError) => {
                    patchState(store, { isLoading: false, commonError: err });
                    if (err.error && err.error['message']) {
                      toastrService.error(err.error['message']);
                    } else if (err.messages && err.messages.length > 0) {
                      toastrService.error(err.messages[0]);
                    } else {
                      toastrService.error('An error occurred while updating location');
                    }
                  },
                })
              );
            } else {
              return locationService.addLocation(location).pipe(
                tapResponse({
                  next: (newLocation: Location) => {
                    const locations = [...store.locations(), newLocation];
                    patchState(store, {
                      locations: locations,
                      isLoading: false,
                      isAddUpdate: true,
                    });
                    toastrService.success(translationService.getValue('LOCATION_ADDED_SUCCESSFULLY'));
                  },
                  error: (err: CommonError) => {
                    patchState(store, { isLoading: false, commonError: err });
                    if (err.error && err.error['message']) {
                      toastrService.error(err.error['message']);
                    } else if (err.messages && err.messages.length > 0) {
                      toastrService.error(err.messages[0]);
                    } else {
                      toastrService.error('An error occurred while adding location');
                    }
                  },
                })
              );
            }
          })
        )
      ),
      getLocationById: rxMethod<string>(
        pipe(
          tap(() => patchState(store, { isLoading: true })),
          switchMap((locationId: string) =>
            locationService.getLocation(locationId).pipe(
              tapResponse({
                next: (location: Location) => {
                  patchState(store, {
                    location: location,
                    isLoading: false,
                  });
                },
                error: (err: CommonError) => {
                  patchState(store, { isLoading: false, commonError: err });
                },
              })
            )
          )
        )
      ),
      resetFlag(){
        patchState(store, { isAddUpdate: false });
      }
    })
  ),
  withHooks({
    onInit(store, securityService = inject(SecurityService)) {
      if (securityService.hasClaim('LOCATION_VIEW_LOCATIONS')) {
        store.loadLocations();
      }
    },
  })
);