package com.example.wtfiimc;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.database.MatrixCursor;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.provider.BaseColumns;
import android.support.v4.app.NavUtils;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleCursorAdapter;
import android.widget.TextView;
//import android.content.Intent;

public class ListEventsActivity extends Activity {
	
	private ListView listEvents;
	String[] columns = new String[]{BaseColumns._ID, "id", "name"};
	String[] col = new String[]{"id", "name"};
	int[] to = new int[] {R.id.number_entry, R.id.name_entry};
	MatrixCursor cursor = new MatrixCursor(columns);
	
	String webserviceURL = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api/";
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		//Intent intent = getIntent();
		setContentView(R.layout.activity_list_events);
		// Show the Up button in the action bar.
		setupActionBar();
		
		listEvents = (ListView)findViewById(R.id.listview);
		String url = webserviceURL + "event";
		new GetAllEvents().execute(url);
		
		listEvents.setOnItemClickListener(new OnItemClickListener(){
			public void onItemClick(AdapterView<?> parent, View view,
		              int position, long id) {
					cursor.moveToPosition((int)id);
					String oname = cursor.getString(2);
					String oid = cursor.getString(1);
		              Intent intnt = new Intent(getApplicationContext(), EventActivity.class);
		              intnt.putExtra("itemid", oid);
		              intnt.putExtra("itemname", oname);
		              startActivity(intnt);

		          }
		});
	}

	private class GetAllEvents extends AsyncTask<String, Integer, String> {
		

		@Override
		protected void onPreExecute() {
		}
		@Override
		protected String doInBackground(String... param){
			String url = param[0];
			InputStream is = null;
			String result = "";
			// http get
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
			/*final ArrayList<String> list = new ArrayList<String>();
			try{
				JSONObject jObject = new JSONObject(result);
				JSONArray resultsArray = jObject.getJSONArray("results");
				for(int i=resultsArray.length()-1; i>= 0; i--){
					JSONObject eventObject = resultsArray.getJSONObject(i);
					
					list.add(eventObject.getString("name"));
				}
				final StableArrayAdapter adapter = new StableArrayAdapter(getApplicationContext(),
				        android.R.layout.simple_list_item_1, list);
				    listEvents.setAdapter(adapter);
			}
			catch(Exception e){
				Log.e("JSON failed", "Error: " +e.toString());
			}*/
			
			try {
				JSONObject jObject = new JSONObject(result);
				JSONArray ja = jObject.getJSONArray("results");
				int key=0;
                for (int i = 0; i < ja.length(); i++) {
                    JSONObject jo = ja.getJSONObject(i);
                    String oid = jo.getString("id");
                    String oname = jo.getString("name");
                    System.out.println(oname);
                    cursor.addRow(new Object[] {key, oid, oname});
                    key++;
                }
                @SuppressWarnings("deprecation")
				ListAdapter adapter = new SimpleCursorAdapter(getApplicationContext(),
                		R.layout.list_item, cursor, col, to);
                listEvents.setAdapter(adapter);
            } catch (JSONException e) {
            	e.printStackTrace();
            	Log.e("JSON failed", "Error: " +e.toString());
            }
			//allEvents.setText(jsonToString.toString());
		}
	}
	/*private class StableArrayAdapter extends ArrayAdapter<String> {

	    HashMap<String, Integer> mIdMap = new HashMap<String, Integer>();

	    public StableArrayAdapter(Context context, int textViewResourceId,
	        List<String> objects) {
	      super(context, textViewResourceId, objects);
	      for (int i = 0; i < objects.size(); ++i) {
	        mIdMap.put(objects.get(i), i);
	      }
	    }

	    @Override
	    public long getItemId(int position) {
	      String item = getItem(position);
	      return mIdMap.get(item);
	    }

	    @Override
	    public boolean hasStableIds() {
	      return true;
	    }

	  }*/
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
		getMenuInflater().inflate(R.menu.list_events, menu);
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
