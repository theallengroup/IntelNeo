Share = {
      base_url: 'http://neo.allengroupdev.com/',//'http://www.outsourcing.pressstart.co/PJCTS/NewIntelNEO/test/projects/onboarding/',
      link: 'images_photo_chose/snapshot1921.png',
      title: 'Intel Neo App',
      description: 'http://www.outsourcing.pressstart.co/PJCTS/NewIntelNEO',
            app_id_fb: '817418414975419',
      fb_share_base:'https://www.facebook.com/dialog/feed?app_id=',
      fb_display:'&display=popup',
      fb_href:"images_photo_chose/",
      fb_redirect:'?mod=usr2session&ac=r_take_photo_choose',
      fb_picture:'false',
      fb_caption:'Click To See My Picture',
      fb_title:'My Photo via Intel Onboarding App',
      media: false,
      facebook: true,
      twitter: true,
      pinterest: true,
      googleplus: true,
      linkedin: true,
      el: '#botonFotosend',
      sharedFB: false,
      sharedLI: false,
      sharedGP: false,
      sharedTW: false,
      sharedPT: false,
    init:function(){
        $(document).on('click',Share.el,function(e){
            e.preventDefault();
            $(Share.el).css('border','1px solid green');
            var list = $(".hideshare-list");
            list.toggle();

        }); 
        //Share.setupSocialLinks();
    },
    getImgPath:function(){
        //var res;
        if(Share.media == false){
            $.ajax({
              type: "POST",
              dataType: 'json',
              url: "./codigo/otro.php",
              data: {'img':$('#droppedimage img').attr('src')},
              success:function(data){
                Share.fb_picture = Share.base_url + Share.fb_href + data.image_path;
                Share.media      = Share.base_url + Share.fb_href + data.image_path;
                Share.setupSocialLinks();
                $.event.trigger('haveShareLink');
              },
              error:function(jqXHR, textStatus, errorThrown){
                $('.fa.fa-spin').hide();
                $.event.trigger('haveShareLink');
              }
           });
        }
    },
    setupSocialLinks:function(){
      $("a.hideshare-facebook").on('click',function(e) {
        e.preventDefault();
        Share.shareFacebook(e);
        if(!Share.sharedFB){
            Share.incrementScore();
            Share.sharedFB = true;
        }
        return false;
      });

      $(".hideshare-twitter").click(function() {
        Share.shareTwitter();
        if(!Share.sharedTW){
            Share.incrementScore();
            Share.sharedTW = true;
        }
        return false;
      });

      $(".hideshare-pinterest").click(function() {
        Share.sharePinterest();
        if(!Share.sharedPT){
            Share.incrementScore();
            Share.sharedPT = true;
        }
        return false;
      });

      $(".hideshare-google-plus").click(function() {
        Share.shareGooglePlus();
        if(!Share.sharedGP){
            Share.incrementScore();
            Share.sharedGP = true;
        }
        return false;
      });

      $(".hideshare-linkedin").click(function() {
        Share.shareLinkedIn();
        if(!Share.sharedLI){
            Share.incrementScore();
            Share.sharedLI = true;
        };
        return false;
      });  
    },
    shareFacebook :function(img) {
        var url = Share.fb_share_base+Share.app_id_fb +Share.fb_display + '&link=' + encodeURIComponent(Share.fb_picture) 
                +  '&picture=' + encodeURIComponent(Share.fb_picture) +'&caption=' + encodeURIComponent(Share.fb_caption) 
                +'&name=' + encodeURIComponent(Share.fb_title) + '&redirect_uri=' + encodeURIComponent(Share.base_url + Share.fb_redirect);
        console.log(url);
        window.open(url ,'Facebook','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
      },
      shareTwitter: function() {
        window.open('https://twitter.com/intent/tweet?original_referer=' + encodeURIComponent(Share.media) + '&text=' + encodeURIComponent(Share.fb_title) + '%20' + encodeURIComponent(Share.media),'Twitter','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
      },
      sharePinterest : function() {
        window.open('//pinterest.com/pin/create/button/?url=' + encodeURIComponent(Share.media) + '&media=' + encodeURIComponent(Share.media) + '&description=' + encodeURIComponent(Share.fb_title),'Pinterest','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
      },
      shareGooglePlus : function() {
        window.open('//plus.google.com/share?url=' + Share.media,'GooglePlus','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
      },
      shareLinkedIn : function() {
        window.open('//www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(Share.media) + '&title=' + encodeURIComponent(Share.fb_title) + '&source=' + encodeURIComponent(Share.media),'LinkedIn','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
      },
      getImgUrl:function(){
        return Share.base_url + Share.fb_href + Share.fb_picture;
      },
      incrementScore: function(isPhoto){
           var shared = (typeof isPhoto == 'undefined') ? '1' : '0';
           console.log(shared);
          $.ajax({
              type: "GET",
              dataType: 'json',
              url: Share.base_url + "?mod=usr2session&ac=r_take_photo_choose&activity_id=27&shared="+shared,
              success:function(data){
                if(data.doRedirect){
                  location.href= Share.base_url+'?mod=usr2session&ac=r_score_read'+data.query_string;
                }
              }
           });
      }
}

      