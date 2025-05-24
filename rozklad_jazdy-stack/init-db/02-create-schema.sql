CREATE SCHEMA rozklad_jazdy AUTHORIZATION rozklad_user;
ALTER ROLE rozklad_user SET search_path TO rozklad_jazdy, public;
