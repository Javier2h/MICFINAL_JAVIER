/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     21/7/2025 11:13:31                           */
/*==============================================================*/


drop table if exists ACTIVIDAD;

drop table if exists ASISTENCIA;

drop table if exists EDUCADOR;

drop table if exists EVALUACIONES;

drop table if exists GRUPOS;

drop table if exists HORARIO;

drop table if exists NINOS;

drop table if exists PERTENECE;

drop table if exists PROGRAMA;

drop table if exists REPRESENTA;

drop table if exists REPRESENTANTE;

/*==============================================================*/
/* Table: ACTIVIDAD                                             */
/*==============================================================*/
create table ACTIVIDAD
(
   ID_ACTIVIDAD         int not null,
   NOMBREAC             char(10) not null,
   DESCRIPCION          text,
   DURACION             decimal(2,1) not null,
   primary key (ID_ACTIVIDAD)
);

/*==============================================================*/
/* Table: ASISTENCIA                                            */
/*==============================================================*/
create table ASISTENCIA
(
   ID_ASISTENCIA        int not null,
   ID_NINO              int,
   FECHA                date not null,
   HORA_ENTRADA         time not null,
   HORA_SALIDA          time not null,
   OBSERVACIONES        text,
   primary key (ID_ASISTENCIA)
);

/*==============================================================*/
/* Table: EDUCADOR                                              */
/*==============================================================*/
create table EDUCADOR
(
   ID_EDUCADORA         int not null,
   NOMBRE               char(50) not null,
   APELLIDO             char(50) not null,
   CEDULA               numeric(10,0) not null,
   ESPECIALIDAD         char(10) not null,
   CELULAR              numeric(10,0),
   EDAD                 int not null,
   PASSWORD             char(255) not null,
   primary key (ID_EDUCADORA)
);

/*==============================================================*/
/* Table: EVALUACIONES                                          */
/*==============================================================*/
create table EVALUACIONES
(
   ID_EVALUACION        int not null,
   ID_NINO              int,
   FECHA                date not null,
   AREA_DESARROLO       char(25) not null,
   DESCRIPCION          text,
   NOTA                 decimal(2,2) not null,
   primary key (ID_EVALUACION)
);

/*==============================================================*/
/* Table: GRUPOS                                                */
/*==============================================================*/
create table GRUPOS
(
   ID_GRUPO             int not null,
   ID_EDUCADORA         int,
   ID_ACTIVIDAD         int not null,
   NOMBRE               char(50) not null,
   UBICACION            text not null,
   primary key (ID_GRUPO)
);

/*==============================================================*/
/* Table: HORARIO                                               */
/*==============================================================*/
create table HORARIO
(
   ID_HORARIO           int not null,
   HORA_INICIO          time not null,
   HORA_FIN             time not null,
   primary key (ID_HORARIO)
);

/*==============================================================*/
/* Table: NINOS                                                 */
/*==============================================================*/
create table NINOS
(
   ID_NINO              int not null,
   NOMBRE               char(50) not null,
   APELLIDO             char(50) not null,
   EDAD                 int not null,
   GENERO               char(10) not null,
   DIRECCION            char(100),
   CEDULA               numeric(10,0) not null,
   ALERGIAS             text not null,
   ENFERMEDADES         text not null,
   OBSERVACIONES        text,
   primary key (ID_NINO)
);

/*==============================================================*/
/* Table: PERTENECE                                             */
/*==============================================================*/
create table PERTENECE
(
   ID_GRUPO             int not null,
   ID_NINO              int not null,
   primary key (ID_GRUPO, ID_NINO)
);

/*==============================================================*/
/* Table: PROGRAMA                                              */
/*==============================================================*/
create table PROGRAMA
(
   ID_ACTIVIDAD         int not null,
   ID_HORARIO           int not null,
   primary key (ID_ACTIVIDAD, ID_HORARIO)
);

/*==============================================================*/
/* Table: REPRESENTA                                            */
/*==============================================================*/
create table REPRESENTA
(
   ID_NINO              int not null,
   ID_REPRESENTANTE     int not null,
   primary key (ID_NINO, ID_REPRESENTANTE)
);

/*==============================================================*/
/* Table: REPRESENTANTE                                         */
/*==============================================================*/
create table REPRESENTANTE
(
   ID_REPRESENTANTE     int not null,
   NOMBRE               char(50) not null,
   APELLIDO             char(50) not null,
   EDAD                 int not null,
   CELULAR              numeric(10,0) not null,
   CEDULA               numeric(10,0) not null,
   PARENTEZCO           char(50) not null,
   _LUGAR_DE_TRABAJO__  text not null,
   GENERO               char(10) not null,
   primary key (ID_REPRESENTANTE)
);

alter table ASISTENCIA add constraint FK_REGISTRA foreign key (ID_NINO)
      references NINOS (ID_NINO) on delete restrict on update restrict;

alter table EVALUACIONES add constraint FK_RINDE foreign key (ID_NINO)
      references NINOS (ID_NINO) on delete restrict on update restrict;

alter table GRUPOS add constraint FK_IMPARTE foreign key (ID_EDUCADORA)
      references EDUCADOR (ID_EDUCADORA) on delete restrict on update restrict;

alter table GRUPOS add constraint FK_REALIZA foreign key (ID_ACTIVIDAD)
      references ACTIVIDAD (ID_ACTIVIDAD) on delete restrict on update restrict;

alter table PERTENECE add constraint FK_PERTENECE foreign key (ID_GRUPO)
      references GRUPOS (ID_GRUPO) on delete restrict on update restrict;

alter table PERTENECE add constraint FK_PERTENECE2 foreign key (ID_NINO)
      references NINOS (ID_NINO) on delete restrict on update restrict;

alter table PROGRAMA add constraint FK_PROGRAMA foreign key (ID_ACTIVIDAD)
      references ACTIVIDAD (ID_ACTIVIDAD) on delete restrict on update restrict;

alter table PROGRAMA add constraint FK_PROGRAMA2 foreign key (ID_HORARIO)
      references HORARIO (ID_HORARIO) on delete restrict on update restrict;

alter table REPRESENTA add constraint FK_REPRESENTA foreign key (ID_NINO)
      references NINOS (ID_NINO) on delete restrict on update restrict;

alter table REPRESENTA add constraint FK_REPRESENTA2 foreign key (ID_REPRESENTANTE)
      references REPRESENTANTE (ID_REPRESENTANTE) on delete restrict on update restrict;

