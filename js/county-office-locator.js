(function() {
  var $, AgriLife, Location,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  $ = jQuery;

  if (!AgriLife) {
    AgriLife = {};
  }

  AgriLife.Location = Location = (function() {
    function Location(cookie) {
      this.cookie = cookie;
      this.locationSuccess = __bind(this.locationSuccess, this);
      $('#county-office-list').change((function(_this) {
        return function(e) {
          _this.getCookieLocation(e.target.value.replace(/'/g, '"'));
          _this.makeCookie();
          return $(e.target).hide();
        };
      })(this));
      this.cookie = !this.cookie ? {} : JSON.parse(this.cookie);
      this.deferred = $.Deferred();
      if ($.isEmptyObject(this.cookie)) {
        this.getNewLocation();
      } else {
        this.getCookieLocation();
      }
    }

    Location.prototype.getNewLocation = function() {
      var locator;
      return locator = navigator.geolocation.getCurrentPosition(this.locationSuccess, this.locationError);
    };

    Location.prototype.makeCookie = function() {
      return $.cookie('tamu_ext_location', JSON.stringify(this.cookie), {
        expires: 7,
        path: '/'
      });
    };

    Location.prototype.getCookieLocation = function(str) {
      if (str) {
        this.cookie = JSON.parse(str);
      }
      return this.showInfo();
    };

    Location.prototype.locationSuccess = function(data) {
      var lat, long;
      lat = data.coords.latitude;
      long = data.coords.longitude;
      return this.getCounty(lat, long);
    };

    Location.prototype.locationError = function(data) {
      return console.log('There was an error');
    };

    Location.prototype.getCounty = function(lat, long) {
      return $.ajax({
        url: 'http://data.fcc.gov/api/block/find',
        data: {
          latitude: lat,
          longitude: long,
          format: 'jsonp',
          showall: false
        },
        dataType: 'jsonp',
        success: (function(_this) {
          return function(data) {
            _this.cookie.lat = lat;
            _this.cookie.long = long;
            _this.cookie.county = data.County.name;
            return _this.cookie.state = data.State.name;
          };
        })(this),
        error: (function(_this) {
          return function(data) {
            return console.log('error');
          };
        })(this)
      }).then((function(_this) {
        return function(data) {
          var counties, office;
          counties = JSON.parse(Ag.counties);
          office = _.findWhere(JSON.parse(Ag.counties), {
            "unit_name": "" + _this.cookie.county + " County Office"
          });
          _this.cookie.phone = office.phone_number;
          return _this.cookie.email = office.email_address;
        };
      })(this)).done((function(_this) {
        return function(data) {
          _this.makeCookie();
          return _this.getCookieLocation();
        };
      })(this));
    };

    Location.prototype.showInfo = function() {
      var contactInfo, template;
      template = $('script#county-info').html();
      contactInfo = _.template(template, this.cookie);
      $('#county-office-location').html(contactInfo);
      if ($('#county-office-list-title').text().indexOf('Not your county?') < 0) {
        $('#county-office-list').hide();
        $('#county-office-list-title').html('Not your county?').wrapInner('<a class="county-office-list-title" href="javascript:;" title="Click to choose your county"></a>').click(function(e) {
          return $('#county-office-list').toggle();
        });
      }
      return this.deferred.resolve();
    };

    return Location;

  })();

  (function($) {
    "use strict";
    return $(function() {
      var agCookie, loc;
      agCookie = $.cookie('tamu_ext_location');
      loc = new AgriLife.Location(agCookie);
      return loc.deferred.done((function(_this) {
        return function() {
          return $(document).foundation('reflow');
        };
      })(this));
    });
  })(jQuery);

}).call(this);
