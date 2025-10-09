<?php
define('_TBL_WEBENGINE_U_CORE_', 'webengine_u_core');
	define('_CORE_UID_', 'uid');
	define('_CORE_UPWD_', 'upwd');
	define('_CORE_UNOM_', 'unom');
	define('_CORE_UEML_', 'ueml');
	define('_CORE_U2FA_', 'u2fa');
	define('_CORE_U2FA_CODE_', 'u2fa_code');
	define('_CORE_U2FA_CREATED_', 'u2fa_created_at');
	define('_CORE_USTATUS_', 'uestado');
	define('_CORE_UONLINE_', 'online');
	define('_CORE_LASTIP_', 'last_ip');
	define('_CORE_LASTLOGIN_', 'last_login');
	define('_CORE_UREG_', 'ureg');

define('_TBL_WEBENGINE_USER_DETAILS_', 'webengine_user_details');
	define('_DETAIL_ID_', 'id');
	define('_DETAIL_UID_', 'user_id');
	define('_DETAIL_FIRSTNAME_', 'first_name');
	define('_DETAIL_MIDDLENAME_', 'middle_name');
	define('_DETAIL_LASTNAME_', 'last_name');
	define('_DETAIL_LASTNAME2_', 'second_last_name');
	define('_DETAIL_BIRTHDATE_', 'birth_date');
	define('_DETAIL_GENDER_', 'gender');
	define('_DETAIL_COUNTRY_', 'country');
	define('_DETAIL_CITY_', 'city');
	define('_DETAIL_ADDRESS_', 'address');
	define('_DETAIL_IDTYPE_', 'id_type');
	define('_DETAIL_IDNUM_', 'id_number');
	define('_DETAIL_UNIVERSITY_', 'university');
	define('_DETAIL_PROGRAM_', 'program');
	define('_DETAIL_SEMESTER_', 'semester');
	define('_DETAIL_INST_EMAIL_', 'institutional_email');
	define('_DETAIL_CARD_CODE_', 'card_code');
	define('_DETAIL_PROFILE_IMG_', 'profile_image');
	define('_DETAIL_PHONE_', 'phone_number');

define('_TBL_WEBENGINE_USER_SESSIONS_', 'webengine_user_sessions');
	define('_SESSION_ID_', 'session_id');
	define('_SESSION_UID_', 'uid');
	define('_SESSION_METHOD_', 'login_method');
	define('_SESSION_IP_', 'ip_address');
	define('_SESSION_AGENT_', 'user_agent');
	define('_SESSION_STARTED_', 'started_at');
	define('_SESSION_ENDED_', 'ended_at');
	define('_SESSION_REASON_', 'ended_reason');

define('_TBL_WEBENGINE_U_AUTH_', 'webengine_u_auth');
	define('_AUTH_ID_', 'id');
	define('_AUTH_UID_', 'uid');
	define('_AUTH_TIME_', 'attempt_time');
	define('_AUTH_IP_', 'ip_address');
	define('_AUTH_COUNTRY_', 'country_code');
	define('_AUTH_AGENT_', 'user_agent');
	define('_AUTH_METHOD_', 'auth_method');
	define('_AUTH_SUCCESS_', 'success');
	define('_AUTH_STATUS_', 'status_code');
	define('_AUTH_REASON_', 'reason');
	define('_AUTH_SESSION_', 'session_id');

define('_TBL_WEBENGINE_BLOCKED_IPS_', 'webengine_blocked_ips');
	define('_BLOCK_IP_ADDR_', 'ip_address');
	define('_BLOCK_UNTIL_', 'block_until');
	define('_BLOCK_REASON_', 'reason');
	define('_BLOCK_CREATED_', 'created_at');
	define('_BLOCK_UPDATED_', 'updated_at');

define('_TBL_WEBENGINE_BLOCK_LOG_', 'webengine_blocked_log');
	define('_BLOCK_LOG_ID_', 'id');
	define('_BLOCK_LOG_UID_', 'uid');
	define('_BLOCK_LOG_TYPE_', 'block_type');
	define('_BLOCK_LOG_REASON_', 'reason');
	define('_BLOCK_LOG_TIMESTAMP_', 'timestamp');

define('_TBL_WEBENGINE_TOKENS_', 'webengine_remember_tokens');
	define('_TOKEN_ID_', 'token_id');
	define('_TOKEN_UID_', 'uid');
	define('_TOKEN_IP_', 'ip_address');
	define('_TOKEN_AGENT_', 'user_agent');
	define('_TOKEN_CREATED_', 'created_at');
	define('_TOKEN_EXPIRES_', 'expires_at');
	define('_TOKEN_LAST_USED_', 'last_used_at');

define('_TBL_WEBENGINE_PASSWORD_RESETS_', 'webengine_password_resets');
	define('_RESET_ID_', 'id');
	define('_RESET_EMAIL_', 'email');
	define('_RESET_TOKEN_', 'token');
	define('_RESET_CREATED_', 'created_at');

define('_TBL_WEBENGINE_ROLES_', 'webengine_roles');
	define('_CLMN_ROLE_ID_', 'id');
	define('_CLMN_ROLE_NAME_', 'name');
	define('_CLMN_ROLE_DESC_', 'description');
	define('_CLMN_ROLE_ESTADO_', 'estado');

define('_TBL_WEBENGINE_USER_ROLES_', 'webengine_user_roles');
	define('_CLMN_USER_ROLE_ID_', 'id');
	define('_CLMN_USER_ROLE_UID_', 'user_id');
	define('_CLMN_USER_ROLE_RID_', 'role_id');

define('_TBL_WEBENGINE_USER_PERMISSIONS_', 'webengine_user_permissions');
	define('_CLMN_USER_PERM_ID_', 'id');
	define('_CLMN_USER_PERM_UID_', 'user_id');
	define('_CLMN_USER_PERM_PERMID_', 'permission_id');

define('_TBL_WEBENGINE_ROLE_PERMISSIONS_', 'webengine_role_permissions');
	define('_CLMN_ROLE_PERM_ID_', 'id');
	define('_CLMN_ROLE_PERM_ROLEID_', 'role_id');
	define('_CLMN_ROLE_PERM_PERMID_', 'permission_id');

define('_TBL_WEBENGINE_PERMISSIONS_', 'webengine_permissions');
	define('_CLMN_PERM_ID_', 'id');
	define('_CLMN_PERM_MODULE_', 'module');
	define('_CLMN_PERM_ACTION_', 'action');
	define('_CLMN_PERM_DESCRIPTION_', 'description');
	define('_CLMN_PERM_ESTADO_', 'estado');

define('_TBL_WEBENGINE_NOTIFICATIONS_', 'webengine_notifications');
	define('_CLMN_NOTIFY_ID_', 'id');
	define('_CLMN_NOTIFY_UID_', 'user_id');
	define('_CLMN_NOTIFY_TYPE_', 'type');
	define('_CLMN_NOTIFY_TITLE_', 'title');
	define('_CLMN_NOTIFY_MESSAGE_', 'message');
	define('_CLMN_NOTIFY_URL_', 'url');
	define('_CLMN_NOTIFY_SEEN_', 'seen');
	define('_CLMN_NOTIFY_CREATED_', 'created_at');

define('_TBL_WEBENGINE_PROJECTS_', 'webengine_projects');
	define('_CLMN_WEBENGINE_PROJECT_ID_', 'id');
	define('_CLMN_WEBENGINE_PROJECT_TITULO_', 'titulo');
	define('_CLMN_WEBENGINE_PROJECT_LINEA_INVESTIGACION_ID_', 'linea_investigacion_id');
	define('_CLMN_WEBENGINE_PROJECT_VISIBILIDAD_', 'visibilidad');
	define('_CLMN_WEBENGINE_PROJECT_ACTIVO_', 'activo');
	define('_CLMN_WEBENGINE_PROJECT_DIRECTORIO_', 'directorio');
	define('_CLMN_WEBENGINE_PROJECT_VERSION_', 'version');
	define('_CLMN_WEBENGINE_PROJECT_FASE_', 'fase');
	define('_CLMN_WEBENGINE_PROJECT_ESTADO_', 'estado');
	define('_CLMN_WEBENGINE_PROJECT_DESCRIPCION_', 'descripcion');
	define('_CLMN_WEBENGINE_PROJECT_PALABRAS_CLAVE_', 'palabras_clave');
	define('_CLMN_WEBENGINE_PROJECT_CALIFICADO_', 'calificado');
	define('_CLMN_WEBENGINE_PROJECT_PUNTUACION_', 'puntuacion');
	define('_CLMN_WEBENGINE_PROJECT_TIMER_SEGUNDOS_', 'timer_segundos');
	define('_CLMN_WEBENGINE_PROJECT_HORA_PROGRAMADA_', 'hora_programada');
	define('_CLMN_WEBENGINE_PROJECT_FECHA_PRESENTACION_', 'fecha_presentacion');
	define('_CLMN_WEBENGINE_PROJECT_CREADO_EN_', 'creado_en');
	define('_CLMN_WEBENGINE_PROJECT_ACTUALIZADO_EN_', 'actualizado_en');

define('_TBL_WEBENGINE_PROJECT_RESEARCHERS_', 'webengine_project_researchers');
	define('_CLMN_PROJRES_ID_', 'id');
	define('_CLMN_PROJRES_PROJID_', 'project_id');
	define('_CLMN_PROJRES_UID_', 'usuario_uid');
	define('_CLMN_PROJRES_ROLE_', 'rol');
	define('_CLMN_PROJRES_STATE_', 'estado');
	define('_CLMN_PROJRES_CREATED_', 'created_at');
	define('_CLMN_PROJRES_UPDATED_', 'updated_at');

define('TABLE_PROJECT_TEACHERS', 'webengine_project_teachers');
	define('COL_PROJECT_TEACHER_ID', 'id');
	define('COL_PROJECT_TEACHER_PROJECT_ID', 'project_id');
	define('COL_PROJECT_TEACHER_USUARIO_UID', 'usuario_uid');
	define('COL_PROJECT_TEACHER_ROL', 'rol');
	define('COL_PROJECT_TEACHER_ESTADO', 'estado');
	define('COL_PROJECT_TEACHER_CREATED_AT', 'created_at');
	define('PROJECT_TEACHER_ROL_DIRECTOR', 'director');
	define('PROJECT_TEACHER_ROL_JURADO', 'jurado');
	define('PROJECT_TEACHER_ROL_ASESOR', 'asesor');
	define('PROJECT_TEACHER_ESTADO_ACTIVO', 'activo');
	define('PROJECT_TEACHER_ESTADO_REMOVIDO', 'removido');

define('DB_PROJECT_REVIEWERS', 'webengine_project_reviewers');
	define('DB_REVIEWER_ID', 'id');
	define('DB_REVIEWER_PROJECT_ID', 'project_id');
	define('DB_REVIEWER_USUARIO_UID', 'usuario_uid');
	define('DB_REVIEWER_ESTADO', 'estado');
	define('DB_REVIEWER_CREATED_AT', 'created_at');

define('DB_RESEARCH_LINES', 'webengine_research_lines');
	define('DB_RESEARCH_LINE_ID', 'id');
	define('DB_RESEARCH_LINE_NOMBRE', 'nombre');
	define('DB_RESEARCH_LINE_ESTADO', 'estado');

define('TABLE_RATINGS', 'webengine_ratings');
	define('RATINGS_ID', 'id');
	define('RATINGS_SESSION_TOKEN', 'session_token');
	define('RATINGS_PROJECT_ID', 'project_id');
	define('RATINGS_EVALUADOR_UID', 'evaluador_uid');
	define('RATINGS_CRITERIO_NOMBRE', 'criterio_nombre');
	define('RATINGS_CRITERIO_VALOR', 'criterio_valor');
	define('RATINGS_CALIFICACION', 'calificacion');
	define('RATINGS_OBSERVACION_PERSONAL', 'observacion_personal');
	define('RATINGS_ESTADO', 'estado');
	define('RATINGS_CREATED_AT', 'created_at');
	define('RATINGS_UPDATED_AT', 'updated_at');

define('TABLE_RATING_SUMMARY', 'webengine_rating_summary');
	define('RATING_SUMMARY_ID', 'rating_summary_id');
	define('RATING_SUMMARY_SESSION_TOKEN', 'session_token');
	define('RATING_SUMMARY_PROJECT_ID', 'project_id');
	define('RATING_SUMMARY_EVALUADOR_UID', 'evaluador_uid');
	define('RATING_SUMMARY_COMENTARIO_GENERAL', 'comentario_general');
	define('RATING_SUMMARY_CALIFICACION_TOTAL', 'calificacion_total');
	define('RATING_SUMMARY_ESTADO_EVALUACION', 'estado_evaluacion');
	define('RATING_SUMMARY_FECHA_INICIO', 'fecha_inicio');
	define('RATING_SUMMARY_FECHA_FIN', 'fecha_fin');
	define('RATING_SUMMARY_TIEMPO_TOTAL', 'tiempo_total');
	define('RATING_SUMMARY_CREATED_AT', 'created_at');
	define('RATING_SUMMARY_UPDATED_AT', 'updated_at');

define('EVALUATION_SESSIONS_TABLE', 'webengine_evaluation_sessions');
	define('EVALUATION_SESSION_FIELD_ID', 'id');
	define('EVALUATION_SESSION_FIELD_PROJECT_ID', 'project_id');
	define('EVALUATION_SESSION_FIELD_CREADO_POR', 'creado_por');
	define('EVALUATION_SESSION_FIELD_STATE', 'estado');
	define('EVALUATION_SESSION_FIELD_TOKEN', 'token_acceso');
	define('EVALUATION_SESSION_FIELD_START', 'inicio');
	define('EVALUATION_SESSION_FIELD_DURATION', 'duracion');
	define('EVALUATION_SESSION_FIELD_END', 'fin');
	define('EVALUATION_SESSION_FIELD_MANUAL_CLOSE', 'cierre_manual');
	define('EVALUATION_SESSION_FIELD_CLOSE_REASON', 'cierre_motivo');
	define('EVALUATION_SESSION_FIELD_CREATED', 'created_at');
	define('EVALUATION_SESSION_FIELD_UPDATED', 'updated_at');

define('_TBL_WEBENGINE_EVALUATION_PARTICIPANTS_', 'webengine_evaluation_participants');
	define('_PARTICIPANT_ID_', 'id');
	define('_PARTICIPANT_SESSION_ID_', 'session_id');
	define('_PARTICIPANT_USER_ID_', 'user_id');
	define('_PARTICIPANT_FECHA_REGISTRO_', 'fecha_registro');
	define('_PARTICIPANT_COMPLETED_', 'completed');
	define('_PARTICIPANT_COMPLETED_AT_', 'completed_at');

define('_TBL_WEBENGINE_REEVALUATIONS_', 'webengine_reevaluations');
	define('_CLMN_REEVALUATION_ID_', 'id');
	define('_CLMN_REEVALUATION_PROJECT_ID_', 'project_id');
	define('_CLMN_REEVALUATION_EVALUADOR_UID_', 'evaluador_uid');
	define('_CLMN_REEVALUATION_ULTIMA_EVALUACION_ID_', 'ultima_evaluacion_id');
	define('_CLMN_REEVALUATION_FECHA_ULTIMA_EVALUACION_', 'fecha_ultima_evaluacion');
	define('_CLMN_REEVALUATION_FECHA_SOLICITUD_', 'fecha_solicitud_reevaluacion');
	define('_CLMN_REEVALUATION_FECHA_LIMITE_', 'fecha_limite_reevaluacion');
	define('_CLMN_REEVALUATION_ESTADO_', 'estado');
	define('_CLMN_REEVALUATION_MOTIVO_SOLICITUD_', 'motivo_solicitud');
	define('_CLMN_REEVALUATION_COMENTARIOS_ADMIN_', 'comentarios_administrador');
	define('_CLMN_REEVALUATION_CREATED_AT_', 'created_at');
	define('_CLMN_REEVALUATION_UPDATED_AT_', 'updated_at');

define('REEVALUATION_ESTADO_PENDIENTE', 'pendiente');
define('REEVALUATION_ESTADO_APROBADA', 'aprobada');
define('REEVALUATION_ESTADO_RECHAZADA', 'rechazada');
define('REEVALUATION_ESTADO_COMPLETADA', 'completada');

define('_TBL_WEBENGINE_BLOG_POSTS_', 'webengine_blog_posts');
	define('_TBL_WEBENGINE_BLOG_POST_CATEGORIES_', 'webengine_blog_post_categories');
	define('_TBL_WEBENGINE_BLOG_COMMENTS_', 'webengine_blog_comments');
	define('_TBL_WEBENGINE_BLOG_CATEGORIES_', 'webengine_blog_categories');

	define('COL_POST_ID', 'id');
	define('COL_POST_TITLE', 'title');
	define('COL_POST_SLUG', 'slug');
	define('COL_POST_CONTENT', 'content');
	define('COL_POST_AUTHOR_ID', 'author_id');
	define('COL_POST_CREATED_AT', 'created_at');
	define('COL_POST_UPDATED_AT', 'updated_at');
	define('COL_POST_PUBLISHED', 'published');
	define('COL_POST_STATUS', 'status');

	define('COL_CATEGORY_ID', 'id');
	define('COL_CATEGORY_NAME', 'name');
	define('COL_CATEGORY_SLUG', 'slug');
	define('COL_CATEGORY_STATUS', 'status');

	define('COL_COMMENT_ID', 'id');
	define('COL_COMMENT_POST_ID', 'post_id');
	define('COL_COMMENT_USER_NAME', 'user_name');
	define('COL_COMMENT_TEXT', 'comment');
	define('COL_COMMENT_CREATED_AT', 'created_at');
	define('COL_COMMENT_STATUS', 'status');

	define('POST_STATUS_ACTIVE', 'active');
	define('POST_STATUS_INACTIVE', 'inactive');
	define('POST_STATUS_DRAFT', 'draft');
	define('POST_STATUS_ARCHIVED', 'archived');

	define('CATEGORY_STATUS_ACTIVE', 'active');
	define('CATEGORY_STATUS_INACTIVE', 'inactive');

	define('COMMENT_STATUS_APPROVED', 'approved');
	define('COMMENT_STATUS_PENDING', 'pending');
	define('COMMENT_STATUS_REJECTED', 'rejected');