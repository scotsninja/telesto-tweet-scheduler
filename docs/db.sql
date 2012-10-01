create table tweets ();

create table campaigns (
campaignId int(11) unsigned not null auto_increment,
campaignUserId int(11) unsigned not null,
campaignName varchar(100) not null,
campaignDescription varchar(255),
campaignDateCreated timestamp,
campaignDateStart datetime not null,
campaignDateEnd datetime not null,
campaignActive tinyint(3) unsigned default 0,
primary key (campaignId),
key (campaignActive),
key (campaignDateStart, campaignDateEnd),
foreign key (campaignUserId) references users(userId) on delete cascade
);

create table campaign_sources (
csId
csCampaignId
csType
csSID
);
create table tweet_history (
thTweetId int(11) unsigned not null,
thSID int(11) unsigned not null,

);

create table schedules ();
create table schedule_ids (
);