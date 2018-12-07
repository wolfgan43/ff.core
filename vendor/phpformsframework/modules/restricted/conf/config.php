<?php
if (!defined("MOD_RES_MEM_CACHING"))				define ("MOD_RES_MEM_CACHING", true); // depends on CM_ENABLE_MEM_CACHING
if (!defined("MOD_RES_MEM_CACHING_BYPATH"))		define ("MOD_RES_MEM_CACHING_BYPATH", true);
if (!defined("MOD_RES_MEM_CACHING_BYDOMAIN"))		define ("MOD_RES_MEM_CACHING_BYDOMAIN", true); // depends on MOD_AUTH_MULTIDOMAIN
if (!defined("MOD_RES_MEM_CACHING_BYUSER"))	    define ("MOD_RES_MEM_CACHING_BYUSER", true); // only when session started
if (!defined("MOD_RES_MEM_CACHING_BYUSERLEVEL"))	define ("MOD_RES_MEM_CACHING_BYUSERLEVEL", true); // only when session started
if (!defined("MOD_RES_MEM_CACHING_BYPROFILE"))		define ("MOD_RES_MEM_CACHING_BYPROFILE", true); // depends ON MOD_SEC_PROFILING

