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
    participants = get_events(event_id)
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
