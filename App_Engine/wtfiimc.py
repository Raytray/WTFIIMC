import webapp2
import json
import urllib2
import datetime

from google.appengine.ext import ndb


class Schedule(ndb.Model):
    json_string = ndb.TextProperty(indexed=False)
    date = ndb.DateTimeProperty(auto_now_add=True)
    event_id = ndb.IntegerProperty()


base_url = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api"

class MainPage(webapp2.RequestHandler):

    def get(self):
        self.response.headers['Content-Type'] = 'text/plain'
        self.response.write('Hello, World!')


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


def get_schedule(event_id):
    event_id = int(event_id)
    event_resource = get_events(event_id)
    if len(event_resource['results']) <= 0:
        return json.dumps({"Error": "Event does not exist."})

    event_resource = event_resource['results'][0]['participants']

    if len(event_resource) <= 0:
        return json.dumps({"Error": "No participants."})

    drivers = [participant for participant in
               event_resource
               if int(participant['can_drive'])]
    riders = [participant for participant in
             event_resource
              if not int(participant['can_drive'])]

    # Error checks
    if (len(riders) > sum([int(driver['seats']) for driver in drivers])):
        return json.dumps({"Error": "More riders than seats available."})

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
    else:
        temp_schedule = None


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


    results = json.dumps({"Error": None, "Groups": drivers_sorted})

    if temp_schedule is not None:
        temp_schedule.json_string=results
    else:
        temp_schedule = Schedule(json_string=results,
                                event_id=event_id)
    temp_schedule.put()

    return results


def get_events(event_id=None):
    if type(event_id) is int:
        results = urllib2.urlopen("{}/event/{}".format(base_url, event_id))
    else:
        results = urllib2.urlopen("{}/event/".format(base_url))

    return json.loads(results.read())


application = webapp2.WSGIApplication([
        ('/', MainPage),
        ('/api/schedule/', event_API),
        ], debug=True)
