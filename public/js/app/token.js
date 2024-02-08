const twilio = require('twilio');

exports.handler = async function(context, event, callback) {
  if (!event.identity || !event.roomname) {
    const response = new twilio.response();
    response.setstatuscode(400);
    response.setbody({
      message: 'missing one of: identity, roomname',
    });
    return callback(null, response);
  }

  // get or create the video room
  const twilioclient = context.gettwilioclient();
  let videoroom;

  try {
    let videoroomlist = await twilioclient.video.rooms.list({limit: 20});
    videoroom = videoroomlist.find(room => room.uniquename = event.roomname);

    if (!videoroom) {
      videoroom = await twilioclient.video.rooms.create({
        uniquename: event.roomname
      });
    }

  } catch (error) {
    const response = new twilio.response();
    response.setstatuscode(401);
    response.setbody({
      message: 'cannot get or create video room',
      error: error
    });
    return callback(null, response);
  }

  // create an access token
  const token = new twilio.jwt.accesstoken(context.account_sid, context.twilio_api_key_sid, context.twilio_api_key_secret);

  // create a video grant
  const videogrant = new twilio.jwt.accesstoken.videogrant({
    room: event.room
  });

  // add the video grant and the user's identity to the token
  token.addgrant(videogrant);
  token.identity = event.identity;

  return callback(null, {
    videoroomsid: videoroom.sid,
    token: token.tojwt(),
  });
}
