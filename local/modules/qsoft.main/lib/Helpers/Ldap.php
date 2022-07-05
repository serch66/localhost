<?php

namespace Qsoft\Main\Helpers;


class Ldap
{
    public static function encodeSid($binary): ?string
    {
        $sid = null;

        if (strlen(decbin(~0)) == 64) {
            /* 64bt PHP */
            // Get revision, indentifier, authority
            $parts = unpack('Crev/x/nidhigh/Nidlow', $binary);
            // Set revision, indentifier, authority
            $sid = sprintf('S-%u-%d',  $parts['rev'], ($parts['idhigh']<<32) + $parts['idlow']);
            // Translate domain
            $parts = unpack('x8/V*', $binary);
            // Append if parts exists
            if ($parts) $sid .= '-';
            // Join all
            $sid.= join('-', $parts);
        } else {
            /* 32bit PHP */
            $sid = 'S-';
            $sidinhex = str_split(bin2hex($binary), 2);
            // Byte 0 = Revision Level
            $sid = $sid.hexdec($sidinhex[0]).'-';
            // Byte 1-7 = 48 Bit Authority
            $sid = $sid.hexdec($sidinhex[6].$sidinhex[5].$sidinhex[4].$sidinhex[3].$sidinhex[2].$sidinhex[1]);
            // Byte 8 count of sub authorities - Get number of sub-authorities
            $subauths = hexdec($sidinhex[7]);
            //Loop through Sub Authorities
            for($i = 0; $i < $subauths; $i++) {
                $start = 8 + (4 * $i);
                // X amount of 32Bit (4 Byte) Sub Authorities
                $sid = $sid.'-'.hexdec($sidinhex[$start+3].$sidinhex[$start+2].$sidinhex[$start+1].$sidinhex[$start]);
            }
        }

        return $sid;
    }
}
