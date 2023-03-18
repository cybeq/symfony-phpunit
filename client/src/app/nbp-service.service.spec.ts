import { TestBed } from '@angular/core/testing';

import { NbpServiceService } from './nbp-service.service';

describe('NbpServiceService', () => {
  let service: NbpServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(NbpServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
