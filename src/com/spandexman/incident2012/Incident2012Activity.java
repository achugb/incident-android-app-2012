package com.spandexman.incident2012;


import android.app.AlertDialog;
import android.app.Dialog;
import android.app.TabActivity;
import android.content.Intent;
import android.content.res.Resources;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TabHost;

public class Incident2012Activity extends TabActivity {
	
	public static final int DIALOG_ABOUT=1;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        Resources res = getResources();
        TabHost tabhost = getTabHost();
        TabHost.TabSpec tab_spec;
        Intent intent;
        
        intent = new Intent().setClass(this, MapActivity.class);
        tab_spec = tabhost.newTabSpec("map").setIndicator("Map", res.getDrawable(android.R.drawable.ic_dialog_map)).setContent(intent);
        tabhost.addTab(tab_spec);
        
        intent = new Intent().setClass(this, ScheduleActivity.class);
        tab_spec = tabhost.newTabSpec("schedule").setIndicator("Schedule", res.getDrawable(android.R.drawable.ic_dialog_dialer)).setContent(intent);
        tabhost.addTab(tab_spec);
        
        intent = new Intent().setClass(this, UpdatesActivity.class);
        tab_spec = tabhost.newTabSpec("updates").setIndicator("Updates", res.getDrawable(android.R.drawable.ic_dialog_info)).setContent(intent);
        tabhost.addTab(tab_spec);
        
        intent = new Intent().setClass(this, PollsActivity.class);
        tab_spec = tabhost.newTabSpec("polls").setIndicator("Polls", res.getDrawable(android.R.drawable.ic_dialog_email)).setContent(intent);
        tabhost.addTab(tab_spec);
        
        tabhost.setCurrentTabByTag("updates");
        
        
        
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu)
    {
    	MenuInflater mnInflater = getMenuInflater();
    	mnInflater.inflate(R.menu.menu, menu);
    	return true;
    }
    
    public boolean onOptionsItemSelected(MenuItem item)
    {
    	switch (item.getItemId())
    	{
    	case R.id.menu_about:
    		showDialog(DIALOG_ABOUT);
    		return true;
		default:
			return super.onOptionsItemSelected(item);
    	}
    }
    
    @Override
    protected Dialog onCreateDialog(int id) {   
    	switch(id)
    	{
    	case DIALOG_ABOUT:
    		return makeAboutDialog();
    	default:
    		return super.onCreateDialog(id);
    	}
    }
    
    private Dialog makeAboutDialog() {
		AlertDialog.Builder ADBuilder = new AlertDialog.Builder(Incident2012Activity.this);
		ADBuilder.setMessage(R.string.about_dialog_message);
		ADBuilder.setTitle("Incident 2012 Android app");
		return ADBuilder.create();
	}
    
}