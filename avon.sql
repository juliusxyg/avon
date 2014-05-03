--
-- Database: `avon`
--

-- --------------------------------------------------------

--
-- Table structure for table `avon_subject`
--

DROP TABLE IF EXISTS `avon_subject`;
CREATE TABLE IF NOT EXISTS `avon_subject` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `mem_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mem_gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '联系人性别 0.男 1.女',
  `mem_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '联系手机',
  `mem_address` varchar(120) NOT NULL DEFAULT '' COMMENT '地址',
  `mem_zip` varchar(20) NOT NULL DEFAULT '' COMMENT '邮编',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '最美一瞬内容',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `from_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发布平台 0.pc 1.mobile',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 0.待审 1.通过',
  `total_vote` int(11) NOT NULL DEFAULT '0' COMMENT '得票总数',
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `avon_subject_vote`
--

DROP TABLE IF EXISTS `avon_subject_vote`;
CREATE TABLE IF NOT EXISTS `avon_subject_vote` (
  `subject_vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL DEFAULT '0' COMMENT '所投主题id',
  `vote_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '投票者ip',
  `vote_time` int(11) NOT NULL DEFAULT '0' COMMENT '投票时间',
  `vote_type` int(11) NOT NULL DEFAULT '0' COMMENT '投票类型 0.点赞 1.答题 2.兑换码',
  `redeem_code` varchar(64) NOT NULL DEFAULT '' COMMENT '所使用的兑换码',
  `from_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发布平台 0.pc 1.mobile',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 0.待审 1.通过',
  PRIMARY KEY (`subject_vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `avon_redeem_code`
--

DROP TABLE IF EXISTS `avon_redeem_code`;
CREATE TABLE IF NOT EXISTS `avon_redeem_code` (
  `redeem_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '兑换码',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否使用 0.未使用 1.已使用',
  PRIMARY KEY (`redeem_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `avon_photo`
--

DROP TABLE IF EXISTS `avon_photo`;
CREATE TABLE IF NOT EXISTS `avon_photo` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `mem_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mem_gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '联系人性别 0.男 1.女',
  `mem_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '联系手机',
  `mem_address` varchar(120) NOT NULL DEFAULT '' COMMENT '地址',
  `mem_zip` varchar(20) NOT NULL DEFAULT '' COMMENT '邮编',
  `photo_url` varchar(255) NOT NULL DEFAULT '' COMMENT '照片地址',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '最美一瞬内容',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '发布时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 0.待审 1.通过',
  `total_vote` int(11) NOT NULL DEFAULT '0' COMMENT '得票总数',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `avon_photo_vote`
--

DROP TABLE IF EXISTS `avon_photo_vote`;
CREATE TABLE IF NOT EXISTS `avon_photo_vote` (
  `photo_vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL DEFAULT '0' COMMENT '所投照片id',
  `vote_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '投票者ip',
  `vote_time` int(11) NOT NULL DEFAULT '0' COMMENT '投票时间',
  PRIMARY KEY (`photo_vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

