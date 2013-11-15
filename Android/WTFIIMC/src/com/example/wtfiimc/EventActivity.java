package com.example.wtfiimc;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;

public class EventActivity extends Activity {
	
	private EditText seatsOpen;
	private EditText personName;
	private TextView returnResults;
	private EditText startDate;
    private EditText endDate;
    private TextView eventInfoResults;
    private TextView rideInfoResults;
    private Button submitButton;
    private CheckBox canDrive;
    
	String webserviceURL = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api/";
	String pyserviceURL = "http://wtfiimc.appspot.com/api/schedule/?id=";
	
	@SuppressLint("NewApi")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_event);
		// Show the Up button in the action bar.
		setupActionBar();
		Intent intent = getIntent();
		final String eventId = intent.getStringExtra("itemid");
		this.getActionBar().setTitle(eventId +" " + intent.getStringExtra("itemname"));
		
		submitButton = (Button)findViewById(R.id.submit_person_button);
		personName = (EditText)findViewById(R.id.person_name);
		seatsOpen = (EditText)findViewById(R.id.num_seats_open);
		startDate = (EditText)findViewById(R.id.start_date);
		endDate = (EditText)findViewById(R.id.end_date);
		returnResults = (TextView)findViewById(R.id.results);
		eventInfoResults = (TextView)findViewById(R.id.event_info_results);
		rideInfoResults = (TextView)findViewById(R.id.ride_info_results);
		canDrive = (CheckBox)findViewById(R.id.check_can_drive);
		
		seatsOpen.setEnabled(false);
		seatsOpen.setFocusable(false);
		
		String url = webserviceURL + "event/"+eventId;
		new getEventInfo().execute(url);
		String pyurl = pyserviceURL + eventId;
		new getRideInfo().execute(pyurl);
		
		submitButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v){
        		if(isEmpty(personName) || isEmpty(startDate) || isEmpty(endDate)) {
        			returnResults.setText("Missing required fields");
        		}
        		else {
        			String check;
        			if(canDrive.isChecked())
        				check = "1";
        			else
        				check = "0";
        			String seats;
        			if(isEmpty(seatsOpen))
        				seats = "0";
        			else
        				seats = seatsOpen.getText().toString();
        			new PostNewParticipant().execute(personName.getText().toString(), check, seats, eventId, startDate.getText().toString(), endDate.getText().toString());
        		
            		personName.setText("");
            		startDate.setText("");
            		endDate.setText("");
            		seatsOpen.setText("");
            		canDrive.setChecked(false);
            		String url = webserviceURL + "event/"+eventId;
            		new getEventInfo().execute(url);
            		String pyurl = pyserviceURL + eventId;
            		new getRideInfo().execute(pyurl);
        		}
        	}
        });
	}
	private boolean isEmpty(EditText etText) {
        return etText.getText().toString().trim().length() == 0;
    }
	private class getEventInfo extends AsyncTask<String, Integer, String> {
		@Override
		protected void onPreExecute() {
		}
		@Override
		protected String doInBackground(String... param){
			String url = param[0];
			InputStream is = null;
			String result = "";
			// http post
			try {
				HttpClient httpclient = new DefaultHttpClient();
				HttpGet httppost = new HttpGet(url);
				HttpResponse response = httpclient.execute(httppost);
				HttpEntity entity = response.getEntity();
				is = entity.getContent();
				Log.d("response code", response.getStatusLine().toString());
				

			} catch (Exception e) {
				Log.e("WTFIIMC", "Error in http connection " + e.toString());
			}

			// convert response to string
			try {
				BufferedReader reader = new BufferedReader(new InputStreamReader(
						is, "iso-8859-1"), 8);
				StringBuilder sb = new StringBuilder();
				String line = null;
				while ((line = reader.readLine()) != null) {
					sb.append(line + "\n");
				}
				is.close();
				result = sb.toString();
			} catch (Exception e) {
				Log.e("WTFIIMC", "Error converting result " + e.toString());
			}
			Log.i("Web", result);
			StringBuilder jsonToString = new StringBuilder();
			try{
				JSONObject jObject = new JSONObject(result).getJSONArray("results").getJSONObject(0);
				JSONArray participants = jObject.getJSONArray("participants");
				
				jsonToString.append("\tEvent name:" + jObject.getString("name") + "\n");
				jsonToString.append("\tDescription:" + jObject.getString("event_info") + "\n");
				jsonToString.append("\tStarts:" + jObject.getString("start_datetime") + "\n");
				jsonToString.append("\tEnds:" + jObject.getString("end_datetime") + "\n\tParticipants:\n");
				
				
				for(int i=0; i < participants.length(); i++){
					JSONObject participant = participants.getJSONObject(i);
					jsonToString.append("\t\t-"+participant.getString("name")+"\n");
				}
				
			}
			catch(Exception e){
				Log.e("JSON failed", "Error: " +e.toString());
			}
			return jsonToString.toString();
		}
		@Override
		protected void onProgressUpdate(Integer... ints) {

		}

		@Override
		protected void onPostExecute(String result) {
			// tells the adapter that the underlying data has changed and it
			// needs to update the view
			eventInfoResults.setText(result);
		}
	}
	
	private class getRideInfo extends AsyncTask<String, Integer, String> {
		@Override
		protected void onPreExecute() {
		}
		@Override
		protected String doInBackground(String... param){
			String url = param[0];
			InputStream is = null;
			String result = "";
			// http post
			try {
				HttpClient httpclient = new DefaultHttpClient();
				HttpGet httppost = new HttpGet(url);
				HttpResponse response = httpclient.execute(httppost);
				HttpEntity entity = response.getEntity();
				is = entity.getContent();
				Log.d("response code", response.getStatusLine().toString());
				

			} catch (Exception e) {
				Log.e("WTFIIMC", "Error in http connection " + e.toString());
			}

			// convert response to string
			try {
				BufferedReader reader = new BufferedReader(new InputStreamReader(
						is, "iso-8859-1"), 8);
				StringBuilder sb = new StringBuilder();
				String line = null;
				while ((line = reader.readLine()) != null) {
					sb.append(line + "\n");
				}
				is.close();
				result = sb.toString();
			} catch (Exception e) {
				Log.e("WTFIIMC", "Error converting result " + e.toString());
			}
			Log.i("Web", result);
			
			return result;
		}
		@Override
		protected void onProgressUpdate(Integer... ints) {

		}

		@Override
		protected void onPostExecute(String result) {
			// tells the adapter that the underlying data has changed and it
			// needs to update the view
			
			StringBuilder jsonToString = new StringBuilder();
			try{
				JSONObject jObject = new JSONObject(result);
				String err = jObject.getString("Error");
				if(err.equals("null")){
					
					JSONArray resultsArray = jObject.getJSONArray("Groups");
					
					for(int i=resultsArray.length()-1; i>= 0; i--){
						JSONObject eventObject = resultsArray.getJSONObject(i);
						
						jsonToString.append("\t" + eventObject.getString("name")+" ("+eventObject.getString("seats")+")\n");
						JSONArray riders = eventObject.getJSONArray("riders");
						for(int j=0; j < riders.length(); j++){
							JSONObject rider = riders.getJSONObject(j);
							jsonToString.append("\t\t-" + rider.getString("name")+"\n");
						}
						
					}
				}
				else {
					jsonToString.append("\t" + err);
				}
			}
			catch(Exception e){
				Log.e("JSON failed", "Error: " +e.toString());
			}
			rideInfoResults.setText(jsonToString.toString());
		}
	}
	
	
	private class PostNewParticipant extends AsyncTask<String, Integer, String> {
		@Override
		protected void onPreExecute() {
		}
		@Override
		protected String doInBackground(String... param){
			String url = webserviceURL + "new/participant";
			HttpClient httpclient = new DefaultHttpClient();
			HttpPost httppost = new HttpPost(url);
			// http post
			try {
				List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
		        nameValuePairs.add(new BasicNameValuePair("name", param[0]));
		        nameValuePairs.add(new BasicNameValuePair("can_drive", param[1]));
		        nameValuePairs.add(new BasicNameValuePair("seats", param[2]));
		        nameValuePairs.add(new BasicNameValuePair("event_id", param[3]));
		        nameValuePairs.add(new BasicNameValuePair("start_datetime", param[4]));
		        nameValuePairs.add(new BasicNameValuePair("end_datetime", param[5]));
		        
		        httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));
				HttpResponse response = httpclient.execute(httppost);
				//HttpEntity entity = response.getEntity();
				//is = entity.getContent();
				Log.d("response code", response.getStatusLine().toString());
				

			} catch (Exception e) {
				Log.e("WTFIIMC", "Error in http connection " + e.toString());
			}
			
			return "Added new participant: " + param[0];
		}
		@Override
		protected void onProgressUpdate(Integer... ints) {

		}

		@Override
		protected void onPostExecute(String result) {
			// tells the adapter that the underlying data has changed and it
			// needs to update the view
			returnResults.setText(result);
		}
	}
	
	public void onCheckboxClicked(View view) {
	    // Is the view now checked?
	    boolean checked = ((CheckBox) view).isChecked();
	    if(checked){
	    	seatsOpen.setEnabled(true);
	    	seatsOpen.setFocusableInTouchMode(true);
	    }
	    else {
	    	seatsOpen.setEnabled(false);
	    	seatsOpen.setText("");
	    	seatsOpen.setFocusable(false);
	    }
	    // Check which checkbox was clicked
	    
	}

	/**
	 * Set up the {@link android.app.ActionBar}, if the API is available.
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	private void setupActionBar() {
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
			getActionBar().setDisplayHomeAsUpEnabled(true);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.event, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case android.R.id.home:
			// This ID represents the Home or Up button. In the case of this
			// activity, the Up button is shown. Use NavUtils to allow users
			// to navigate up one level in the application structure. For
			// more details, see the Navigation pattern on Android Design:
			//
			// http://developer.android.com/design/patterns/navigation.html#up-vs-back
			//
			NavUtils.navigateUpFromSameTask(this);
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

}
