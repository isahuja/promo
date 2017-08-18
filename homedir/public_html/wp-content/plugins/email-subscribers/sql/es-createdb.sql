CREATE TABLE IF NOT EXISTS es_emaillist (
  es_email_id INT unsigned NOT NULL AUTO_INCREMENT,
  es_email_name VARCHAR(255) NOT NULL,
  es_email_mail VARCHAR(255) NOT NULL,
  es_email_status VARCHAR(25) NOT NULL default 'Unconfirmed',
  es_email_created datetime NOT NULL default '0000-00-00 00:00:00',
  es_email_viewcount VARCHAR(100) NOT NULL,
  es_email_group VARCHAR(255) NOT NULL default 'Public',
  es_email_guid VARCHAR(255) NOT NULL,
  PRIMARY KEY  (es_email_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS es_templatetable (
  es_templ_id INT unsigned NOT NULL AUTO_INCREMENT,
  es_templ_heading VARCHAR(255) NOT NULL,
  es_templ_body TEXT NULL,
  es_templ_status VARCHAR(25) NOT NULL default 'Published',
  es_email_type VARCHAR(100) NOT NULL default 'Newsletter',
  PRIMARY KEY  (es_templ_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS es_notification (
  es_note_id INT unsigned NOT NULL AUTO_INCREMENT,
  es_note_cat TEXT NULL,
  es_note_group VARCHAR(255) NOT NULL,
  es_note_templ INT unsigned NOT NULL,
  es_note_status VARCHAR(10) NOT NULL default 'Enable',
  PRIMARY KEY  (es_note_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS es_sentdetails (
  es_sent_id INT unsigned NOT NULL AUTO_INCREMENT,
  es_sent_guid VARCHAR(255) NOT NULL,
  es_sent_qstring VARCHAR(255) NOT NULL,
  es_sent_source VARCHAR(255) NOT NULL,
  es_sent_starttime datetime NOT NULL default '0000-00-00 00:00:00',
  es_sent_endtime datetime NOT NULL default '0000-00-00 00:00:00',
  es_sent_count INT unsigned NOT NULL,
  es_sent_preview TEXT NULL,
  es_sent_status VARCHAR(25) NOT NULL default 'Sent',
  es_sent_type VARCHAR(25) NOT NULL default 'Immediately',
  es_sent_subject VARCHAR(255) NOT NULL,
  PRIMARY KEY  (es_sent_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- SQLQUERY ---

CREATE TABLE IF NOT EXISTS es_deliverreport (
  es_deliver_id INT unsigned NOT NULL AUTO_INCREMENT,
  es_deliver_sentguid VARCHAR(255) NOT NULL,
  es_deliver_emailid INT unsigned NOT NULL,
  es_deliver_emailmail VARCHAR(255) NOT NULL,
  es_deliver_sentdate datetime NOT NULL default '0000-00-00 00:00:00',
  es_deliver_status VARCHAR(25) NOT NULL,
  es_deliver_viewdate datetime NOT NULL default '0000-00-00 00:00:00',
  es_deliver_sentstatus VARCHAR(25) NOT NULL default 'Sent',
  es_deliver_senttype VARCHAR(25) NOT NULL default 'Immediately',
  PRIMARY KEY  (es_deliver_id)
) ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;
