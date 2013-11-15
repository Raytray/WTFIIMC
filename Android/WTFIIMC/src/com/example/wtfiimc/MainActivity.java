package com.example.wtfiimc;

import android.os.AsyncTask;
import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.view.Menu;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.DateSorter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.ArrayAdapter;
import android.widget.ListView;


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

public class MainActivity extends Activity {

    private Button submitButton;
    private EditText eventField;
    private EditText nameField;
    private EditText descField;
    private EditText startDate;
    private EditText endDate;
    private TextView returnResults;
    private TextView currentEvents;
    
    ListView courseList;
    String webserviceURL = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api/";
    
    
	@Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        
        submitButton = (Button)findViewById(R.id.submit_button);
        nameField = (EditText)findViewById(R.id.owner_name);
        returnResults = (TextView)findViewById(R.id.results);
        eventField = (EditText)findViewById(R.id.event_name);
        descField = (EditText)findViewById(R.id.event_desc);
        startDate = (EditText)findViewById(R.id.start_date);
        endDate = (EditText)findViewById(R.id.end_date);
        
        currentEvents = (TextView)findViewById(R.id.current_events);
        getCurrentEvents();
        
        submitButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v){
        		if(isEmpty(nameField) || isEmpty(eventField) || isEmpty(descField) || isEmpty(startDate) || isEmpty(endDate)) {
        			returnResults.setText("Missing required fields");
        		}
        		else {
        			//returnResults.setText(nameField.getText().toString() + " created the event: " + eventField.getText().toString());
        			
        			new PostNewEvent().execute(eventField.getText().toString(), descField.getText().toString(), startDate.getText().toString(), endDate.getText().toString());
        			
            		Log.i("Web", "submitted");
            		Log.i("Web", nameField.getText().toString());
            		nameField.setText("");
            		eventField.setText("");
            		descField.setText("");
            		startDate.setText("");
            		endDate.setText("");
            		getCurrentEvents();
        		}
        	}
        });
    }
	private class PostNewEvent extends AsyncTask<String, Integer, String> {
		@Override
		protected void onPreExecute() {
		}
		@Override
		protected String doInBackground(String... param){
			String url = webserviceURL + "new/event";
			HttpClient httpclient = new DefaultHttpClient();
			HttpPost httppost = new HttpPost(url);
			// http post
			try {
				List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(2);
		        nameValuePairs.add(new BasicNameValuePair("name", param[0]));
		        nameValuePairs.add(new BasicNameValuePair("event_info", param[1]));
		        nameValuePairs.add(new BasicNameValuePair("start_datetime", param[2]));
		        nameValuePairs.add(new BasicNameValuePair("end_datetime", param[3]));
		        httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));
				HttpResponse response = httpclient.execute(httppost);
				//HttpEntity entity = response.getEntity();
				//is = entity.getContent();
				Log.d("response code", response.getStatusLine().toString());
				

			} catch (Exception e) {
				Log.e("LousList", "Error in http connection " + e.toString());
			}
			
			return "Added new event: " + param[0];
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
	public void listEvents(View view) {
		Intent intent = new Intent(this, ListEventsActivity.class);
	    startActivity(intent);
	}
	
	private class GetCurrentEvents extends AsyncTask<String, Integer, String> {
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
				Log.e("LousList", "Error converting result " + e.toString());
			}
			Log.i("Web", result);
			StringBuilder jsonToString = new StringBuilder();
			try{
				JSONObject jObject = new JSONObject(result);
				JSONArray resultsArray = jObject.getJSONArray("results");
				int limitFive = resultsArray.length()-5;
				if(limitFive < 0)
					limitFive=0;
				for(int i=resultsArray.length()-1; i>= limitFive; i--){
					JSONObject eventObject = resultsArray.getJSONObject(i);
					
					jsonToString.append(eventObject.getString("id")+" "+eventObject.getString("name")+"\n");
					
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
			currentEvents.setText(result);
		}
	}
	private void getCurrentEvents(){
		String url = webserviceURL + "event";
		
		new GetCurrentEvents().execute(url);
		
		
		
		
	}
	private boolean isEmpty(EditText etText) {
        return etText.getText().toString().trim().length() == 0;
    }
	
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
}
