/* global PushClient, EncryptionHelperFactory, MaterialComponentsSnippets */
/* eslint-env browser */
class AppController {
  constructor() {
    // Define a different server URL here if desire.
    this._PUSH_SERVER_URL = '';
    this._API_KEY = 'AIzaSyBBh4ddPa96rQQNxqiq_qQj7sq1JdsNQUQ';

    this._applicationKeys = {
      publicKey: window.base64UrlToUint8Array(
        'BDd3_hVL9fZi9Ybo2UUzA284WG5FZR30_95YeZJsiA' +
        'pwXKpNcF1rRPF3foIiBHXRdJI2Qhumhf6_LFTeZaNndIo'),
      privateKey: window.base64UrlToUint8Array(
        'xKZKYRNdFFn8iQIF2MH54KTfUHwH105zBdzMR7SI3xI')
    };

    // This div contains the UI for CURL commands to trigger a push
    this._sendPushOptions = document.querySelector('.js-send-push-options');
    this._payloadTextField = document.querySelector('.js-payload-textfield');
    this._stateMsg = document.querySelector('.js-state-msg');
   
    // Below this comment is code to initialise a material design lite view.
   // const toggleSwitch = document.querySelector('.js-push-toggle-switch');
     this.ready = Promise.resolve();
      this._uiInitialised();
  }

_uiInitialised() {
this._stateChangeListener = this._stateChangeListener.bind(this);
        this._subscriptionUpdate = this._subscriptionUpdate.bind(this);
        //this._toggleSwitch = toggleSwitch;
        this._pushClient = new PushClient(
                this._stateChangeListener,
                this._subscriptionUpdate,
                this._applicationKeys.publicKey
                );
        this._pushClient.subscribeDevice();
}

  registerServiceWorker() {
    // Check that service workers are supported
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('./service-worker.js')
      .catch(err => {
        this.showErrorMessage(
          'Unable to Register SW',
          'Sorry this demo requires a service worker to work and it ' +
          'failed to install - sorry :('
        );
        console.error(err);
      });
    } else {
      this.showErrorMessage(
        'Service Worker Not Supported',
        'Sorry this demo requires service worker support in your browser. ' +
        'Please try this demo in Chrome or Firefox Nightly.'
      );
    }
  }

  _stateChangeListener(state, data) {
    if (typeof state.interactive !== 'undefined') {
      if (state.interactive) {
      } else {
      }
    }

    if (typeof state.pushEnabled !== 'undefined') {
      if (state.pushEnabled) {
      } else {
      }
    }

    switch (state.id) {
      case 'UNSUPPORTED':
        this.showErrorMessage(
          'Push Not Supported',
          data
        );
        break;
      case 'ERROR':
        this.showErrorMessage(
          'Ooops a Problem Occurred',
          data
        );
        break;
      default:
        break;
    }
  }

  _subscriptionUpdate(subscription) {
    this._currentSubscription = subscription;
    if (!subscription) {
      return;
    }

    // This is too handle old versions of Firefox where keys would exist
    // but auth wouldn't
   
    const subscriptionObject = JSON.parse(JSON.stringify(subscription));
    if (
      subscriptionObject &&
      subscriptionObject.keys &&
      subscriptionObject.keys.auth &&
      subscriptionObject.keys.p256dh) {
    } else {
    }

    this.updatePushInfo();
  }

  updatePushInfo() {
     let payloadPromise = Promise.resolve(null);
    // Vapid support
    const vapidPromise = EncryptionHelperFactory.createVapidAuthHeader(
      this._applicationKeys,
      this._currentSubscription.endpoint,
      'mailto:simple-push-demo@gauntface.co.uk');

    return Promise.all([
      payloadPromise,
      vapidPromise
    ])
    .then(results => {
      const payload = results[0];
      const vapidHeaders = results[1];

      let infoFunction = this.getWebPushInfo;
      infoFunction = () => {
        return this.getWebPushInfo(this._currentSubscription, payload,
          vapidHeaders);
      };
      if (this._currentSubscription.endpoint.indexOf(
        'https://android.googleapis.com/gcm/send') === 0) {
        infoFunction = () => {
          return this.getGCMInfo(this._currentSubscription, payload,
            this._API_KEY);
        };
      }

      const requestInfo = infoFunction();

    });
  }

  getGCMInfo(subscription, payload, apiKey) {
    const headers = {};

    headers.Authorization = `key=${apiKey}`;
    headers['Content-Type'] = `application/json`;

    const endpointSections = subscription.endpoint.split('/');
    const subscriptionId = endpointSections[endpointSections.length - 1];
    const gcmAPIData = {
      to: subscriptionId
    };

    if (payload) {
      gcmAPIData['raw_data'] = this.toBase64(payload.cipherText); // eslint-disable-line
      headers.Encryption = `salt=${payload.salt}`;
      headers['Crypto-Key'] = `dh=${payload.publicServerKey}`;
      headers['Content-Encoding'] = `aesgcm`;
    }

    return {
      headers: headers,
      body: JSON.stringify(gcmAPIData),
      endpoint: 'https://android.googleapis.com/gcm/send'
    };
  }

  getWebPushInfo(subscription, payload, vapidHeaders) {
      console.log(subscription.endpoint);
     // alert('getWebpushinfo');
    let body = null;
    const headers = {};
    headers.TTL = 60;

    if (payload) {
      body = payload.cipherText;

      headers.Encryption = `salt=${payload.salt}`;
      headers['Crypto-Key'] = `dh=${payload.publicServerKey}`;
      headers['Content-Encoding'] = 'aesgcm';
    } else {
      headers['Content-Length'] = 0;
    }

    if (vapidHeaders) {
      headers.Authorization = `Bearer ${vapidHeaders.bearer}`;

      if (headers['Crypto-Key']) {
        headers['Crypto-Key'] = `${headers['Crypto-Key']}; ` +
          `p256ecdsa=${vapidHeaders.p256ecdsa}`;
      } else {
        headers['Crypto-Key'] = `p256ecdsa=${vapidHeaders.p256ecdsa}`;
      }
    }
    console.log(subscription.endpoint);
    console.log(headers.Authorization);
    console.log(headers['Crypto-Key']);
    const response = {
      headers: headers,
      endpoint: subscription.endpoint
    };

    if (body) {
      response.body = body;
    }
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]+ "/" + getUrl.pathname.split('/')[2];
    var subcribe_url = baseUrl + '/subscribe';
    jQuery.ajax({
         type : "POST",
         url: subcribe_url,
         data: {
             'authorization':headers.Authorization,
             'cryptokey':headers['Crypto-Key'],
             'endpoint':subscription.endpoint},
         success : function(response){
              console.log(response);
         },   
    });
    return response;
  }

  toBase64(arrayBuffer, start, end) {
    start = start || 0;
    end = end || arrayBuffer.byteLength;

    const partialBuffer = new Uint8Array(arrayBuffer.slice(start, end));
    return btoa(String.fromCharCode.apply(null, partialBuffer));
  }

  showErrorMessage(title, message) {
   alert(message);
  }
}

if (window) {
  window.AppController = AppController;
}
