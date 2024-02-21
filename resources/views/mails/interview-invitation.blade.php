<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<html lang="en">

<head data-id="__react-email-head"></head>

<body data-id="__react-email-body" style="background-color:#f6f9fc;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,Roboto,&quot;Helvetica Neue&quot;,Ubuntu,sans-serif">
<table align="center" width="100%" data-id="__react-email-container" role="presentation" cellSpacing="0" cellPadding="0" border="0" style="max-width:37.5em;background-color:#ffffff;margin:0 auto;padding:20px 0 48px;margin-bottom:64px">
    <tbody>
    <tr style="width:100%">
        <td>
            <table align="center" width="100%" data-id="react-email-section" style="padding:0 48px" border="0" cellPadding="0" cellSpacing="0" role="presentation">
                <tbody>
                <tr>
                    <td><img data-id="react-email-img" alt="Sint" src="{{secure_asset('assets/BlueLogo.png')}}" width="85" height="37" style="display:block;outline:none;border:none;text-decoration:none" />
                        <hr data-id="react-email-hr" style="width:100%;border:none;border-top:1px solid #eaeaea;border-color:#e6ebf1;margin:20px 0" />
                        <p data-id="react-email-text" style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Hi, {{$invitation?->name}}</p>
                        <p data-id="react-email-text" style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">We are pleased to notify you that a scheduled interview for the position of {{$invitation?->vacancy?->title}} at {{$invitation?->vacancy?->organization?->name}} will take place at your own convenience.<br> Please click the button below to begin the interview @if($invitation->exired_at), keeping in mind that the link will expire on{{$invitation->exired_at?->format('Y-m-d H:i')}} @endif</p><a href="{{$invitation?->url}}" data-id="react-email-link" target="_blank" style="color:#fff;text-decoration:none;background-color:#0036ce;border-radius:5px;font-size:16px;font-weight:bold;text-align:center;display:block;width:100%;padding:10px 0">Start your Interview</a>
                        <hr data-id="react-email-hr" style="width:100%;border:none;border-top:1px solid #eaeaea;border-color:#e6ebf1;margin:20px 0" />
                        <p data-id="react-email-text" style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Wish you best of luck,
                        <p data-id="react-email-text" style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">{{$invitation?->vacancy?->organization->name}}</p>
                        </p>
                        <p data-id="react-email-text" style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Sint Team</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>
