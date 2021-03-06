$ = jQuery
AgriLife = {} if not AgriLife

AgriLife.Location = class Location

  constructor: (@cookie) ->
    $('#county-office-list').change (e) =>
      @getCookieLocation(e.target.value.replace(/'/g,'"'))
      @makeCookie()
      $(e.target).hide()

    @cookie = if not @cookie then {} else JSON.parse(@cookie);

    @deferred = $.Deferred()

    if $.isEmptyObject @cookie then @getNewLocation() else @getCookieLocation()

  getNewLocation: () ->
    locator = navigator.geolocation.getCurrentPosition @locationSuccess, @locationError
    return

  makeCookie: () ->
    $.cookie 'tamu_ext_location', JSON.stringify(@cookie),
      expires: 7
      path: '/'

  getCookieLocation: (str) ->
    if str then @cookie = JSON.parse(str)
    @showInfo()

  locationSuccess: (data) =>
    lat = data.coords.latitude
    long = data.coords.longitude
    @getCounty(lat, long)

  locationError: (data) ->
    err = 'There was an error';
    if typeof data == 'object'
      if typeof data.message is 'string' and data.message.length > 0
        err += ': ' + data.message
      else if typeof data.code is 'number'
        switch data.code
          when 1 then err += ': Permission denied'
          when 2 then err += ': Position unavailable'
          when 3 then err += ': Timeout'
          else break
    console.log err;

  getCounty: (lat, long) ->
    $.ajax(
      url: '//data.fcc.gov/api/block/find'
      data:
        latitude: lat
        longitude: long
        format: 'jsonp'
        showall: false
      dataType: 'jsonp'
      success: (data) =>
        @cookie.lat = lat
        @cookie.long = long
        @cookie.county = data.County.name
        @cookie.state = data.State.name
        return
      error: (data) =>
        console.log('error');
    ).then( (data) =>
      counties = JSON.parse(Ag.counties)
      office = _.findWhere( JSON.parse(Ag.counties), { "unit_name": "#{@cookie.county} County Office" } )
      @cookie.phone = office.phone_number
      @cookie.email = office.email_address
      return
    ).done( (data) =>
      @makeCookie()
      @getCookieLocation()
    )

  showInfo: () ->
    saveSettings = _.templateSettings
    _.templateSettings = interpolate : /\{\{(.+?)\}\}/g
    template = _.template $('script#county-info').html()
    _.templateSettings = saveSettings
    contactInfo = template @cookie
    $('#county-office-location').html(contactInfo)
    if $('#county-office-list-title').text().indexOf('Not your county?') < 0
      $('#county-office-list').hide()
      $('#county-office-list-title')
        .html('Not your county?')
        .wrapInner('<a class="county-office-list-title" href="javascript:;" title="Click to choose your county"></a>')
        .click (e) ->
          $('#county-office-list').toggle()
    @deferred.resolve()

do ($ = jQuery) ->
  "use strict"
  $ ->
    agCookie = $.cookie('tamu_ext_location')
    loc = new AgriLife.Location(agCookie)
    loc.deferred.done () =>
      $(document).foundation('reflow')
