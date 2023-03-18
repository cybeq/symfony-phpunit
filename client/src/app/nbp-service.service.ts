import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class NbpServiceService {

  constructor(private http:HttpClient) { }

   getFromApi(currencyCode:any, startDate:any, endDate:any){
      return this.http.post('/api/req', {currencyCode, startDate, endDate});
   }
}
