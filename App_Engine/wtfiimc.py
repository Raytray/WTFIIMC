import webapp2
import json
import urllib2


base_url = "http://plato.cs.virginia.edu/~cs4720f13cucumber/api"

class MainPage(webapp2.RequestHandler):

    def get(self):
        self.response.headers['Content-Type'] = 'text/plain'
        self.response.write('Hello, World!')


class event_API(webapp2.RequestHandler):

    def get(self):
        event_id = self.request.get('id')
        schedule = get_schedule(event_id)

        self.response.headers['Content-Type'] = 'text/plain'
        self.response.write(schedule)


def get_schedule(event_id):
    event_resource = get_events(event_id)

    drivers = [participant for participant in
               event_resource['participants'] if participant['can_drive']]
    riders = [participant for participant in
             event_resource['participants'] if not participant['can_drive']]

    # Do a check for more riders than seats available (len(rider) > sum([driver['seats'] for driver in drivers]) or something like that.
    # Do other checks as necessary (more drivers than needed, no riders, no drivers, etc. Special cases? How do we deal with this dynamically?

    riders_sorted = sorted(riders, key=lambda rider: rider['end_datetime'], reverse=True) # I think reverse=True should set it to end first comes up first. If not, just remove ', reverse=True'

    for driver in drivers:
        while driver['seats'] > len(driver['riders']):
            driver['riders'] = rider.pop() # take rider from the end of the list

    # Do a final check to see all driver have riders, else, stick them into someone elses car if there is space, or a car full of drivers?

    return participants


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