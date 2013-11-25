import webapp2
import sendgrid
import json
import datetime
import ConfigParser

from google.appengine.ext import ndb
from google.appengine.api import urlfetch

class Schedule(ndb.Model):
    json_string = ndb.TextProperty(indexed=False)
    date = ndb.DateTimeProperty(auto_now_add=True)
    event_id = ndb.IntegerProperty()


base_url = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api"

class MainPage(webapp2.RequestHandler):

    def get(self):
        self.response.headers['Content-Type'] = 'text/plain'
        self.response.write('Go to http://wtfiimc.appengine.com/api/schedule/?id=ID!')


class event_API(webapp2.RequestHandler):

    def get(self):
        event_id = self.request.get('id')
        schedule = get_schedule(event_id)

        self.response.headers['Content-Type'] = 'application/json'
        callback = self.request.get('callback')
        if callback:
           callback = self.request.get('callback')
           jsonp_schedule = callback + "(" + schedule + ")"
           self.response.write(jsonp_schedule)
        else:
            self.response.write(schedule)

class email_API(webapp2.RequestHandler):

    def get(self):
        config = ConfigParser.RawConfigParser()
        config.read('sendgrid.cfg')
        email = config.get('sendgrid', 'email')
        password = config.get('sendgrid', 'password')
        s = sendgrid.Sendgrid(email.strip("'").strip('"'), password.strip("'").strip('"'))
        message = sendgrid.Message("raytray25@gmail.com", "test email!", "this is the first test email for cucumber")
        message.add_to("ryli721@gmail.com", "Richard Li")
        s.web.send(message)
        self.response.write("Message sent!")


def get_schedule(event_id):
    event_id = int(event_id)
    event_resource = get_events(event_id)
    if len(event_resource['results']) <= 0:
        return json.dumps({"Error": "Event does not exist.", "Date": datetime.datetime.now().isoformat()})

    event_resource = event_resource['results'][0]['participants']

    if len(event_resource) <= 0:
        return json.dumps({"Error": "No participants.", "Date": datetime.datetime.now().isoformat()})

    drivers = [participant for participant in
               event_resource
               if int(participant['can_drive'])]
    riders = [participant for participant in
             event_resource
              if not int(participant['can_drive'])]

    # Error checks
    if (len(riders) > sum([int(driver['seats']) for driver in drivers])):
        return json.dumps({"Error": "More riders than seats available.", "Date": datetime.datetime.now().isoformat()})

    latest = sorted(event_resource,
                    key=lambda participant: participant['created_datetime'],
                    reverse=True)[0]['created_datetime']

    latest = datetime.datetime.strptime(latest, "%Y-%m-%d %H:%M:%S")
    latest = latest + datetime.timedelta(hours=5)

    temp_schedule = Schedule.query(Schedule.event_id == event_id).fetch(1)
    if len(temp_schedule) > 0:
        temp_schedule = temp_schedule[0]
        if temp_schedule.date > latest:
            return temp_schedule.json_string

    drivers_sorted = sorted(drivers,
                            key=lambda driver: driver['seats'],
                            reverse = True)

    while len(riders) <= sum([int(driver['seats']) for driver in drivers_sorted]):
        riders.append(drivers_sorted.pop())

    if (len(riders) > sum([int(driver['seats']) for driver in drivers_sorted])):
        drivers_sorted.append(riders.pop())

    riders_sorted = sorted(riders,
                           key=lambda rider: rider['end_datetime'],
                           reverse = True)


    for driver in drivers_sorted:
        driver.setdefault('riders', [])
        driver['seats'] = int(driver['seats'])
        while (driver['seats'] > len(driver['riders']) and
               len(riders_sorted) > 0):
            driver['riders'].append(riders_sorted.pop())


    results = json.dumps({"Error": None, "Groups": drivers_sorted, "Date": datetime.datetime.now().isoformat()})

    temp_schedule = Schedule(json_string=results,
                             event_id=event_id)
    temp_schedule.put()
    return results


def get_events(event_id=None):
    if type(event_id) is int:
        results = urlfetch.fetch("{}/event/{}".format(base_url, event_id), headers={'Cache-Control': 'max-age=1'})
    else:
        results = urlfetch.fetch("{}/event/".format(base_url), headers={'Cache-Control': 'max-age=1'})

    return json.loads(results.content)


application = webapp2.WSGIApplication([
        ('/', MainPage),
        ('/api/schedule/', event_API),
        ('/api/email/', email_API),
        ], debug=True)
