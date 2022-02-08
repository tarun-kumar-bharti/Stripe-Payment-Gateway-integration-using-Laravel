<style>
table, td, th {
  border: 1px solid black;
}

table {
  width: 100%;
  border-collapse: collapse;
}
</style> 

<p style="margin-top:5px;margin-bottom:0px;">Dear Administrator,</p>
<br>
<p style="text-align:justify;">
    User posted one notary appointment. Following are the details.

    <br><br>
	
		<table style="width:60%">
		   
		  <tr>
			<td>Name</td>
			<td><b>{{$notaObj->fname}} {{$notaObj->lname}}</td>
		  </tr>
		  <tr>
			<td>Email</td>
			<td> <b>{{$notaObj->email}}</b></td>
		  </tr>
		  
		  <tr>
			<td>Contact</td>
			<td> <b>{{$notaObj->contact}}</b></td>
		  </tr>
		  
		  <tr>
			<td>Preferred Date/Time Slot</td>
			<td> <b>{{$notaObj->slottiming}}</b></td>
		  </tr>
		  
		  <tr>
			<td>Notary Service Needed</td>
			<td> <b>{{$notaObj->notaryservice}}</b></td>
		  </tr>
		  
		   <tr>
			<td>Preferences</td>
			<td> <b>{{$notaObj->preferences}}</b></td>
		  </tr>
		  
		  
	</table>
 
	<br><br><br><br>
	Thank you.<br>
	 
</p>
<br> 
