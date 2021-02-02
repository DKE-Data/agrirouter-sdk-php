<?php

namespace App\Definitions {

    /**
     * Definition for all capabilities.
     * @package App\Definitions
     */
    class CapabilityTypeDefinitions
    {

        /**
         * Type 'iso:11783:-10:taskdata:zip'.
         */
        public const ISO_11783_TASKDATA_ZIP = "iso:11783:-10:taskdata:zip";

        /**
         * Type 'iso:11783:-10:device_description:protobuf'.
         */
        public const ISO_11783_DEVICE_DESCRIPTION_PROTOBUF = "iso:11783:-10:device_description:protobuf";

        /**
         * Type 'iso:11783:-10:time_log:protobuf'.
         */
        public const ISO_11783_TIMELOG_PROTOBUF = "iso:11783:-10:time_log:protobuf";

        /**
         * Type 'img:bmp'.
         */
        public const IMG_BMP = "img:bmp";

        /**
         * Type 'img:jpeg'.
         */
        public const IMG_JPEG = "img:jpeg";

        /**
         * Type 'img:png'.
         */
        public const IMG_PNG = "img:png";

        /**
         * Type 'shp:shape:zip'.
         */
        public const SHP_SHAPE_ZIP = "shp:shape:zip";

        /**
         * Type 'doc:pdf'.
         */
        public const DOC_PDF = "doc:pdf";

        /**
         * Type 'vid:avi'.
         */
        public const VID_AVI = "vid:avi";

        /**
         * Type 'vid:mp4'.
         */
        public const VID_MP4 = "vid:mp4";

        /**
         * Type 'vid:wmv'.
         */
        public const VID_WMV = "vid:wmv";

        /**
         * Type 'gps:info'.
         */
        public const GPS_INFO = "gps:info";

    }
}