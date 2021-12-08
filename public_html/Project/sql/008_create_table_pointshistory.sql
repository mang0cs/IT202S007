CREATE TABLE PointsHistory
(
    id            int auto_increment,
    user_id       int,
    points_change int,
    reason        varchar(50), 
    created       datetime default current_timestamp,
    primary key (id),
    foreign key (user_id) references Users (id)
)