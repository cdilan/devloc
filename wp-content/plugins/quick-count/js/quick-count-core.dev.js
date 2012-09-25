var quick_count = jQuery.extend(quick_count || {}, {
    cdata: {},
    map_loaded: false,
    tooltip_width: 0,
    tooltip_height: 0,
    tooltip: jQuery('<div/>').attr('id', 'quick-count-tooltip').appendTo(jQuery('body')),
    cflag_html: function(code){
        return ['<img class="quick-count-flag" src="',
            quick_count.quick_flag_url,
            '/',
            code,
            '.gif" />'].join('');
    },
    map_cflag_html: function(code){
        return ['<img class="quick-count-flags quick-count-map-flag" src="',
            quick_count.quick_flag_url,
            '/',
            code,
            '.gif" />'].join('');
    },
    single_user_html: function(single_user, counter){
        var backend =  (quick_count.action() === 'quick-count-backend') ? 1 : 0;

        var str = [quick_count.i18n.count_s];

        if(backend)
            str.push(quick_count.i18n.ip_s);

        if(single_user.cc != null)
            str.push(quick_count.i18n.country_s);

        str.push(quick_count.i18n.joined_s);

        if(single_user.bn != null)
            str.push(quick_count.i18n.browser_s);
        else
            str.push(quick_count.i18n.agent_s);

        if(single_user.r != null && backend)
            str.push(quick_count.i18n.referrer_s);

        return ([
            '<div class="quick-count-list-single-data">',
            str.join(' ').
            replace('%count', counter).
            replace('%name', single_user.n).
            replace('%ip', single_user.i).
            replace('%cname', single_user.cn).
            replace('%cflag', quick_count.cflag_html(single_user.cc)).
            replace('%joined', single_user.j).
            replace('%polled', single_user.p).
            replace(new RegExp('%url', 'g'), single_user.u).
            replace('%title', single_user.t).
            replace(new RegExp('%referrer', 'g'), single_user.r).
            replace('%bname', single_user.bn).
            replace('%bversion', single_user.bv).
            replace('%pname', single_user.pn).
            replace('%pversion', single_user.pv).
            replace(new RegExp('%agent', 'g'), single_user.a),
            '</div>'].join(''));
    },
    update: function(){
        jQuery.post(quick_count.ajaxurl, {
            action: quick_count.action(),
            u: document.URL,
            t: document.title,
            r: document.referrer},
            function(data){
                if(typeof(quick_count.users_interval) == "undefined"){
                    quick_count.users_interval = setInterval(function(){
                        quick_count.update();
                    }, quick_count.timeout_refresh_users);
                }

                var i = 0,j = 0;
                var admins = Array(), subscribers = Array(), bots = Array(), visitors = Array(), countries = Array();
                var online_count_s = [];
                var admins_s = [], subscribers_s = [], bots_s = [], visitors_s = [];
                var admins_count_s = '', subscribers_count_s='', bots_count_s = '', visitors_count_s = '',  all_count_s = [];
                var admins_names_s = [], subscribers_names_s = [], bots_names_s = [];
                var by_country_s = [];

                var ul = data.ul;
                for(i=0;typeof(ul[i])!="undefined";i++){
                    if(ul[i].s == 0){
                        admins.push(ul[i]);
                    }else if(ul[i].s == 1){
                        subscribers.push(ul[i]);
                    }else if(ul[i].s == 3){
                        bots.push(ul[i]);
                    }else if(ul[i].s == 2){
                        visitors.push(ul[i]);
                    }

                    if(ul[i].cc){
                        var country_exists = 0;
                        for(j=0;typeof(countries[j])!="undefined";j++)
                            if(countries[j].users[0].cc == ul[i].cc){
                                countries[j].users.push(ul[i]);
                                country_exists = 1;
                                break;
                            }

                        if(country_exists == 0)
                            countries.push({users: [ul[i]]});
                    }
               }

                if(admins.length != 0){
                    admins_s.push('<div class="quick-count-list-group"><div class="quick-count-list-group-title">');
                    if(admins.length == 1){
                        admins_s.push(quick_count.i18n.one_admin_online_s);
                        admins_count_s = quick_count.i18n.one_admin_s;
                    }else {
                        admins_s.push(quick_count.i18n.multiple_admins_online_s.replace('%number', admins.length));
                        admins_count_s = quick_count.i18n.multiple_admins_s.replace('%number', admins.length);
                    }
                    admins_s.push('</div>');
                    for(i=0;typeof(admins[i])!="undefined";i++){
                        admins_s.push(quick_count.single_user_html(admins[i], i+1));
                        admins_names_s.push(['<strong>',admins[i].n, '</strong>'].join(''));
                    }
                    admins_s.push('</div>');
                    all_count_s.push([admins_count_s, ' (', admins_names_s.join(', '), ')'].join(''));
                }

                if(subscribers.length != 0){
                    subscribers_s.push('<div class="quick-count-list-group"><div class="quick-count-list-group-title">');
                    if(subscribers.length == 1){
                        subscribers_s.push(quick_count.i18n.one_subscriber_online_s);
                        subscribers_count_s = quick_count.i18n.one_subscriber_s;
                    }else {
                        subscribers_s.push(quick_count.i18n.multiple_subscribers_online_s.replace('%number', subscribers.length));
                        subscribers_count_s = quick_count.i18n.multiple_subscribers_s.replace('%number', subscribers.length);
                    }
                    subscribers_s.push('</div>');
                    for(i=0;typeof(subscribers[i])!="undefined";i++){
                        subscribers_s.push(quick_count.single_user_html(subscribers[i], admins.length+i+1));
                        subscribers_names_s.push(['<strong>', subscribers[i].n, '</strong>'].join(''));
                    }
                    subscribers_s.push('</div>');
                    all_count_s.push([subscribers_count_s, ' (', subscribers_names_s.join(', '), ')'].join(''));
                }

                if(bots.length != 0){
                    bots_s.push('<div class="quick-count-list-group"><div class="quick-count-list-group-title">');
                    if(bots.length == 1){
                        bots_s.push(quick_count.i18n.one_bot_online_s);
                        bots_count_s = quick_count.i18n.one_bot_s;
                    }else {
                        bots_s.push(quick_count.i18n.multiple_bots_online_s.replace('%number', bots.length));
                        bots_count_s = quick_count.i18n.multiple_bots_s.replace('%number', bots.length);
                    }
                    bots_s.push('</div>');
                    for(i=0;typeof(bots[i])!="undefined";i++){
                        bots_s.push(quick_count.single_user_html(bots[i], admins.length+subscribers.length+i+1));
                        bots_names_s.push(['<strong>', bots[i].n, '</strong>'].join(''));
                    }
                    bots_s.push('</div>');
                    all_count_s.push([bots_count_s, ' (', bots_names_s.join(', '), ')'].join(''));
                }

                if(visitors.length != 0){
                    visitors_s.push('<div class="quick-count-list-group"><div class="quick-count-list-group-title">');
                    if(visitors.length == 1){
                        visitors_s.push(quick_count.i18n.one_visitor_online_s);
                        visitors_count_s = quick_count.i18n.one_visitor_s;
                    }else {
                        visitors_s.push(quick_count.i18n.multiple_visitors_online_s.replace('%number', visitors.length));
                        visitors_count_s = quick_count.i18n.multiple_visitors_s.replace('%number', visitors.length);
                    }
                    visitors_s.push('</div>');
                    for(i=0;typeof(visitors[i])!="undefined";i++){
                        visitors_s.push(quick_count.single_user_html(visitors[i], admins.length+subscribers.length+bots.length+i+1));
                    }
                    visitors_s.push('</div>');
                    all_count_s.push(visitors_count_s);
                }

                if(jQuery('div.quick-count-online-count').length != 0){
                    if(data.ul.length == 0)
                        online_count_s.push(quick_count.i18n.zero_s);
                    else {
                        if(data.ul.length == 1){
                            online_count_s.push(quick_count.i18n.one_s);
                        }else{
                            online_count_s.push(quick_count.i18n.multiple_s.replace('%number', ul.length));
                        }

                        if(countries.length > 0){
                            if(countries.length == 1)
                                online_count_s.push(quick_count.i18n.one_country_s);
                            else
                                online_count_s.push(quick_count.i18n.multiple_countries_s.replace('%number', countries.length));
                        }
                    }
                    online_count_s.push(quick_count.i18n.online_s);
                    jQuery('div.quick-count-online-count').html(online_count_s.join(' '));
                }

                if(jQuery('div.quick-count-online-count-each').length != 0){
                    jQuery('div.quick-count-online-count-each').html(all_count_s.join(', '));
                }

                if(jQuery('div.quick-count-by-country').length != 0 && data.qfc == 1){
                    countries.sort(function(a, b) {
                        if ( a.users.length > b.users.length )
                            return -1;
                        if ( a.users.length < b.users.length )
                            return 1;

                        if ( a.users[0].cn < b.users[0].cn )
                            return -1;
                        if ( a.users[0].cn > b.users[0].cn )
                            return 1;
                        return 0;
                    });

                     for(i=0;typeof(countries[i])!="undefined";i++)
                         by_country_s.push([countries[i].users[0].cn, quick_count.cflag_html(countries[i].users[0].cc), ['(<strong>', countries[i].users.length, '</strong>)'].join('')].join(' '));

                    jQuery('div.quick-count-by-country').html(by_country_s.join(', '));
                }

                if(jQuery('div.quick-count-most-online').length != 0){
                    var most_online_s = quick_count.i18n.most_online_s.replace('%number', data.sn).replace('%time', data.st);
                    jQuery('div.quick-count-most-online').html(most_online_s);
                }

                if(jQuery('div.quick-count-list').length != 0){
                    jQuery('div.quick-count-list').html([admins_s.join(''), subscribers_s.join(''), bots_s.join(''), visitors_s.join('')].join(''));
                }

                jQuery('div.quick-count-visitors-map').each(function(){
                    var self = jQuery(this);
                    if(self.length != 0 && data.qfc == 1){
                        if(quick_count.action() === 'quick-count-backend')
                            self.css({'width': '60%'});
                        else
                            self.css({'width': '95%'});

                        var map_w = self.width();
                        self.height(Math.round(2/3 * map_w));

                        for(var c in quick_count.cdata){
                            quick_count.cdata[c] = 0;
                        }

                        for(i=0;typeof(countries[i])!="undefined";i++){
                            quick_count.cdata[countries[i].users[0].cc.toLowerCase()] = countries[i].users.length;
                        }

                        if(self.children().length == 0){
                            self.vectorMap({
                                map: 'world_en',
                                hoverOpacity: 0.7,
                                enableZoom: true,
                                showTooltip: false,
                                values: quick_count.cdata,
                                onRegionOver: function(el, code, region){
                                    var html = [region, quick_count.map_cflag_html(code.toUpperCase())]
                                    if(typeof(quick_count.cdata[code]) != 'undefined')
                                        html.push('('+quick_count.cdata[code]+')');
                                    else
                                        html.push('(0)');

                                    quick_count.tooltip.html(html.join(' ')).show();
                                    quick_count.tooltip_width = quick_count.tooltip.width();
                                    quick_count.tooltip_height = quick_count.tooltip.height();
                                },
                                onRegionOut: function(){
                                    quick_count.tooltip.hide();
                                },

                                backgroundColor: '#505050',
                                color: '#ffffff',
                                hoverColor: 'black',
                                selectedColor: 'black',
                                scaleColors: ['#D0ECD2', '#109618']
                            });
                        }else{
                            self.vectorMap('set', 'values', quick_count.cdata);
                        }
                    }
               });
            },
            'json'
        );
    }
});

jQuery('div.quick-count-visitors-map').mousemove(function(e){
    if(quick_count.tooltip.is(':visible')){
      quick_count.tooltip.css({
        left: e.pageX - 15 - quick_count.tooltip_width,
        top: e.pageY - 15 - quick_count.tooltip_height
      });
    }
});

quick_count.update();