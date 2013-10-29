package com.example.wtfiimc;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class MainActivity extends Activity {

    private Button submitButton;
    private EditText eventField;
    private EditText nameField;
    private EditText descField;
    private EditText startDate;
    private EditText endDate;
    private TextView returnResults;
    private TextView currentEvents;
	
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
        getCurrentEvents(currentEvents);
        
        submitButton.setOnClickListener(new OnClickListener() {
        	public void onClick(View v){
        		if(isEmpty(nameField) || isEmpty(eventField) || isEmpty(descField) || isEmpty(startDate) || isEmpty(endDate)) {
        			returnResults.setText("Missing required fields");
        		}
        		else {
        			returnResults.setText(nameField.getText().toString() + " created the event: " + eventField.getText().toString());
        			
            		Log.i("Web", "submitted");
            		Log.i("Web", nameField.getText().toString());
            		nameField.setText("");
            		eventField.setText("");
            		descField.setText("");
            		startDate.setText("");
            		endDate.setText("");
        		}
        	}
        });
    }

	private void getCurrentEvents(TextView tv){
		tv.setText("hello");
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
